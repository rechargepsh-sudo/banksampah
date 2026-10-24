<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UpdateNasabahRequest;
use App\Http\Requests\Kader\RegisterNasabahRequest;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KaderNasabahWebController extends Controller
{
    /**
     * Menampilkan daftar nasabah untuk Kader dan Admin.
     */
    public function index(Request $request): View
    {
        $kader = $request->user();
        $query = Nasabah::query()->with(['user', 'kaderPendaftar']);

        // Filter pencarian: nama, NIK, atau nomor HP
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Filter nomor RW & RT
        if ($request->filled('nomor_rw')) {
            $query->where('nomor_rw', $request->query('nomor_rw'));
        }

        if ($request->filled('nomor_rt')) {
            $query->where('nomor_rt', $request->query('nomor_rt'));
        }

        // Filter: Hanya nasabah binaan saya
        if ($request->query('lingkup') === 'saya') {
            $query->where('created_by', $kader->id);
        }

        $nasabahList = $query->orderBy('nama', 'asc')->paginate(10)->withQueryString();

        $stats = [
            'total_nasabah' => Nasabah::count(),
            'nasabah_saya' => Nasabah::where('created_by', $kader->id)->count(),
            'total_saldo' => Nasabah::sum('saldo_aktif'),
        ];

        return view('kader.nasabah.index', compact('nasabahList', 'stats'));
    }

    /**
     * Menampilkan formulir pendaftaran nasabah baru khusus Kader.
     */
    public function create(): View
    {
        return view('kader.nasabah.create');
    }

    /**
     * Menyimpan data nasabah baru beserta akun login user.
     */
    public function store(RegisterNasabahRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $kader = $request->user();

        // 1. Tentukan atau auto-generate username
        if (! empty($validated['username'])) {
            $username = $validated['username'];
        } else {
            $firstName = Str::slug(explode(' ', trim($validated['nama']))[0], '');
            $nikSuffix = substr($validated['nik'], -4);
            $baseUsername = "nsb_{$firstName}_{$nikSuffix}";
            $username = $baseUsername;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = "{$baseUsername}_{$counter}";
                $counter++;
            }
        }

        // 2. Tentukan atau auto-generate password awal (default 6 digit terakhir NIK)
        $plainPassword = ! empty($validated['password'])
            ? $validated['password']
            : substr($validated['nik'], -6);

        // 3. Tentukan tanggal bergabung (default hari ini jika kosong)
        $tanggalBergabung = ! empty($validated['tanggal_bergabung'])
            ? $validated['tanggal_bergabung']
            : now()->toDateString();

        // 4. Sesuaikan kader pembina berdasarkan hierarki wilayah (Kecamatan -> Kelurahan -> RW)
        $kaderWilayahQuery = User::where('role', 'kader')->where('nomor_rw', $validated['nomor_rw']);
        if (! empty($validated['kelurahan'])) {
            $kaderWilayahQuery->where('kelurahan', $validated['kelurahan']);
        }
        if (! empty($validated['kecamatan'])) {
            $kaderWilayahQuery->where('kecamatan', $validated['kecamatan']);
        }
        $kaderRw = $kaderWilayahQuery->first();
        if (! $kaderRw) {
            // Fallback matching by RW saja jika tidak match persis kelurahannya
            $kaderRw = User::where('role', 'kader')
                ->where('nomor_rw', $validated['nomor_rw'])
                ->first();
        }
        $pembinaId = $kaderRw ? $kaderRw->id : $kader->id;

        // 5. Simpan data user dan nasabah dalam database transaction
        $nasabah = DB::transaction(function () use ($validated, $username, $plainPassword, $pembinaId, $tanggalBergabung) {
            $user = User::create([
                'name' => $validated['nama'],
                'username' => $username,
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($plainPassword),
                'role' => 'nasabah',
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kelurahan' => $validated['kelurahan'] ?? null,
                'nomor_rw' => $validated['nomor_rw'],
                'is_active' => true,
            ]);

            return Nasabah::create([
                'user_id' => $user->id,
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kelurahan' => $validated['kelurahan'] ?? null,
                'nomor_rw' => $validated['nomor_rw'],
                'nomor_rt' => $validated['nomor_rt'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'tanggal_bergabung' => $tanggalBergabung,
                'saldo_aktif' => 0.00,
                'created_by' => $pembinaId,
            ]);
        });

        $pesanSukses = "Nasabah {$nasabah->nama} berhasil didaftarkan! Akun Login: Username '{$username}', Password awal: '{$plainPassword}'. Berikan kredensial ini kepada warga untuk login mandiri.";

        return redirect()->route('kader.nasabah.show', $nasabah)
            ->with('success', $pesanSukses);
    }

    /**
     * Menampilkan detail profil nasabah, saldo aktif, riwayat setoran & mutasi.
     */
    public function show(Nasabah $nasabah): View
    {
        $nasabah->load([
            'user',
            'kaderPendaftar',
            'transaksiSetor' => fn ($q) => $q->orderBy('tanggal_setor', 'desc')->take(5),
            'mutasiSaldo' => fn ($q) => $q->orderBy('created_at', 'desc')->take(10),
        ]);

        return view('kader.nasabah.show', compact('nasabah'));
    }

    /**
     * Menampilkan form edit data nasabah untuk Kader.
     */
    public function edit(Nasabah $nasabah): View
    {
        $nasabah->load(['user', 'kaderPendaftar']);

        return view('kader.nasabah.edit', compact('nasabah'));
    }

    /**
     * Memperbarui data nasabah dan akun login user terkait oleh Kader.
     */
    public function update(UpdateNasabahRequest $request, Nasabah $nasabah): RedirectResponse
    {
        $validated = $request->validated();

        // Sesuaikan kader pembina berdasarkan hierarki wilayah (Kecamatan -> Kelurahan -> RW)
        $kaderWilayahQuery = User::where('role', 'kader')->where('nomor_rw', $validated['nomor_rw']);
        if (! empty($validated['kelurahan'])) {
            $kaderWilayahQuery->where('kelurahan', $validated['kelurahan']);
        }
        if (! empty($validated['kecamatan'])) {
            $kaderWilayahQuery->where('kecamatan', $validated['kecamatan']);
        }
        $kaderRw = $kaderWilayahQuery->first();
        if (! $kaderRw) {
            $kaderRw = User::where('role', 'kader')
                ->where('nomor_rw', $validated['nomor_rw'])
                ->first();
        }
        $pembinaId = $kaderRw ? $kaderRw->id : $nasabah->created_by;

        DB::transaction(function () use ($validated, $nasabah, $pembinaId, $request) {
            $nasabahUpdate = [
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kelurahan' => $validated['kelurahan'] ?? null,
                'nomor_rw' => $validated['nomor_rw'],
                'nomor_rt' => $validated['nomor_rt'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'created_by' => $pembinaId,
            ];

            if (! empty($validated['tanggal_bergabung'])) {
                $nasabahUpdate['tanggal_bergabung'] = $validated['tanggal_bergabung'];
            }

            $nasabah->update($nasabahUpdate);

            if ($nasabah->user) {
                $userUpdate = [
                    'name' => $validated['nama'],
                    'username' => ! empty($validated['username']) ? $validated['username'] : $nasabah->user->username,
                    'email' => $validated['email'] ?? null,
                    'is_active' => $request->boolean('is_active', true),
                ];

                if (! empty($validated['password'])) {
                    $userUpdate['password'] = Hash::make($validated['password']);
                }

                $nasabah->user->update($userUpdate);
            }
        });

        return redirect()->route('kader.nasabah.index')->with('success', "Data nasabah {$nasabah->nama} berhasil diperbarui.");
    }

    /**
     * Menghapus nasabah dan akun user terkait oleh Kader.
     */
    public function destroy(Nasabah $nasabah): RedirectResponse
    {
        $nama = $nasabah->nama;

        DB::transaction(function () use ($nasabah) {
            if ($nasabah->user) {
                $nasabah->user->delete();
            } else {
                $nasabah->delete();
            }
        });

        return redirect()->route('kader.nasabah.index')->with('success', "Nasabah {$nama} berhasil dihapus dari sistem.");
    }
}
