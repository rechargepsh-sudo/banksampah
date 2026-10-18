<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNasabahRequest;
use App\Http\Requests\Admin\UpdateNasabahRequest;
use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NasabahController extends Controller
{
    /**
     * Menampilkan daftar nasabah dengan fitur pencarian dan filter untuk Admin.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Nasabah::query()->with(['user:id,username,email,is_active', 'kaderPendaftar:id,name']);

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

        $nasabahList = $query->orderBy('id', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $nasabahList,
        ], 200);
    }

    /**
     * Pendaftaran nasabah baru beserta akun login user oleh Admin.
     */
    public function store(StoreNasabahRequest $request): JsonResponse
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

        $result = DB::transaction(function () use ($validated, $username, $plainPassword, $saldoAwal, $kaderId, $request) {
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

            return ['nasabah' => $nasabah, 'user' => $user];
        });

        return response()->json([
            'success' => true,
            'message' => 'Nasabah baru berhasil didaftarkan.',
            'data' => [
                'nasabah' => $result['nasabah']->load(['user', 'kaderPendaftar']),
                'credentials' => [
                    'username' => $username,
                    'initial_password' => $plainPassword,
                ],
            ],
        ], 201);
    }

    /**
     * Menampilkan detail satu nasabah beserta riwayat transaksi.
     */
    public function show(Nasabah $nasabah): JsonResponse
    {
        $nasabah->load(['user', 'kaderPendaftar'])
            ->loadCount(['transaksiSetor', 'transaksiTarikDana', 'mutasiSaldo']);

        return response()->json([
            'success' => true,
            'data' => $nasabah,
        ], 200);
    }

    /**
     * Memperbarui data nasabah dan akun login user terkait oleh Admin.
     */
    public function update(UpdateNasabahRequest $request, Nasabah $nasabah): JsonResponse
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

        return response()->json([
            'success' => true,
            'message' => 'Data nasabah berhasil diperbarui.',
            'data' => $nasabah->fresh()->load(['user', 'kaderPendaftar']),
        ], 200);
    }

    /**
     * Menghapus nasabah dan akun user terkait oleh Admin.
     */
    public function destroy(Nasabah $nasabah): JsonResponse
    {
        DB::transaction(function () use ($nasabah) {
            if ($nasabah->user) {
                $nasabah->user->delete();
            } else {
                $nasabah->delete();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Nasabah berhasil dihapus.',
        ], 200);
    }
}
