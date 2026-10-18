<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNasabahRequest;
use App\Http\Requests\Admin\UpdateNasabahRequest;
use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NasabahController extends Controller
{
    /**
     * Menampilkan daftar nasabah dengan fitur pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $query = Nasabah::query()->with(['user', 'kaderPendaftar']);

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('nomor_rw')) {
            $query->where('nomor_rw', $request->query('nomor_rw'));
        }

        if ($request->filled('nomor_rt')) {
            $query->where('nomor_rt', $request->query('nomor_rt'));
        }

        $nasabahList = $query->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.nasabah.index', compact('nasabahList'));
    }

    /**
     * Menampilkan form pendaftaran nasabah baru.
     */
    public function create(): View
    {
        $kaders = User::where('role', 'kader')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.nasabah.create', compact('kaders'));
    }

    /**
     * Menyimpan data nasabah baru beserta akun login user.
     */
    public function store(StoreNasabahRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $admin = $request->user();

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

        // 2. Tentukan atau auto-generate password
        $plainPassword = ! empty($validated['password'])
            ? $validated['password']
            : substr($validated['nik'], -6);

        $saldoAwal = isset($validated['saldo_awal']) ? (float) $validated['saldo_awal'] : 0.0;
        $kaderId = ! empty($validated['kader_id']) ? (int) $validated['kader_id'] : $admin->id;

        DB::transaction(function () use ($validated, $username, $plainPassword, $saldoAwal, $kaderId, $request) {
            $user = User::create([
                'name' => $validated['nama'],
                'username' => $username,
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($plainPassword),
                'role' => 'nasabah',
                'is_active' => $request->boolean('is_active', true),
            ]);

            $nasabah = Nasabah::create([
                'user_id' => $user->id,
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'nomor_rw' => $validated['nomor_rw'],
                'nomor_rt' => $validated['nomor_rt'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'saldo_aktif' => $saldoAwal,
                'created_by' => $kaderId,
            ]);

            if ($saldoAwal > 0) {
                MutasiSaldo::create([
                    'nasabah_id' => $nasabah->id,
                    'jenis_mutasi' => 'masuk',
                    'nominal' => $saldoAwal,
                    'saldo_sebelum' => 0.00,
                    'saldo_sesudah' => $saldoAwal,
                    'tipe_referensi' => 'saldo_awal',
                    'deskripsi' => 'Saldo awal pendaftaran nasabah oleh Admin',
                ]);
            }
        });

        $infoMsg = "Nasabah berhasil didaftarkan. Akun login: username '{$username}', password '{$plainPassword}'.";

        return redirect()->route('admin.nasabah.index')->with('success', $infoMsg);
    }

    /**
     * Menampilkan profil lengkap nasabah beserta riwayat transaksi.
     */
    public function show(Nasabah $nasabah): View
    {
        $nasabah->load(['user', 'kaderPendaftar']);

        $setoranTerbaru = $nasabah->transaksiSetor()
            ->with(['kader', 'details.kategoriSampah'])
            ->latest('tanggal_setor')
            ->limit(5)
            ->get();

        $penarikanTerbaru = $nasabah->transaksiTarikDana()
            ->with('diprosesOleh')
            ->latest('tanggal_tarik')
            ->limit(5)
            ->get();

        $mutasiTerbaru = $nasabah->mutasiSaldo()
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.nasabah.show', compact('nasabah', 'setoranTerbaru', 'penarikanTerbaru', 'mutasiTerbaru'));
    }

    /**
     * Menampilkan form edit data nasabah.
     */
    public function edit(Nasabah $nasabah): View
    {
        $nasabah->load(['user', 'kaderPendaftar']);

        $kaders = User::where('role', 'kader')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.nasabah.edit', compact('nasabah', 'kaders'));
    }

    /**
     * Memperbarui data nasabah dan akun login terkait.
     */
    public function update(UpdateNasabahRequest $request, Nasabah $nasabah): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $nasabah, $request) {
            $nasabah->update([
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'nomor_rw' => $validated['nomor_rw'],
                'nomor_rt' => $validated['nomor_rt'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'created_by' => ! empty($validated['kader_id']) ? (int) $validated['kader_id'] : $nasabah->created_by,
            ]);

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

        return redirect()->route('admin.nasabah.index')->with('success', 'Data nasabah berhasil diperbarui.');
    }

    /**
     * Menghapus nasabah dan akun user terkait dari sistem.
     */
    public function destroy(Nasabah $nasabah): RedirectResponse
    {
        DB::transaction(function () use ($nasabah) {
            if ($nasabah->user) {
                $nasabah->user->delete();
            } else {
                $nasabah->delete();
            }
        });

        return redirect()->route('admin.nasabah.index')->with('success', 'Nasabah berhasil dihapus.');
    }
}
