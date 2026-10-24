<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UpdateNasabahRequest;
use App\Http\Requests\Kader\RegisterNasabahRequest;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KaderController extends Controller
{
    /**
     * Pendaftaran nasabah baru oleh Kader (auto-generate akun login user).
     */
    public function registerNasabah(RegisterNasabahRequest $request): JsonResponse
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

        // 2. Tentukan atau auto-generate password awal
        $plainPassword = ! empty($validated['password'])
            ? $validated['password']
            : substr($validated['nik'], -6); // Default 6 digit terakhir NIK

        // 3. Tentukan tanggal bergabung
        $tanggalBergabung = ! empty($validated['tanggal_bergabung'])
            ? $validated['tanggal_bergabung']
            : now()->toDateString();

        // 4. Sesuaikan kader pembina berdasarkan RW nasabah jika tersedia
        $kaderRw = User::where('role', 'kader')
            ->where('nomor_rw', $validated['nomor_rw'])
            ->first();
        $pembina = $kaderRw ?? $kader;

        // 5. Simpan akun user dan profil nasabah dalam database transaction
        $result = DB::transaction(function () use ($validated, $username, $plainPassword, $pembina, $tanggalBergabung) {
            $user = User::create([
                'name' => $validated['nama'],
                'username' => $username,
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($plainPassword),
                'role' => 'nasabah',
                'is_active' => true,
            ]);

            $nasabah = Nasabah::create([
                'user_id' => $user->id,
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'nomor_rw' => $validated['nomor_rw'],
                'nomor_rt' => $validated['nomor_rt'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'tanggal_bergabung' => $tanggalBergabung,
                'saldo_aktif' => 0.00,
                'created_by' => $pembina->id,
            ]);

            return [
                'user' => $user,
                'nasabah' => $nasabah,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Nasabah baru dan akun login berhasil didaftarkan.',
            'data' => [
                'nasabah' => [
                    'id' => $result['nasabah']->id,
                    'user_id' => $result['nasabah']->user_id,
                    'nik' => $result['nasabah']->nik,
                    'nama' => $result['nasabah']->nama,
                    'no_hp' => $result['nasabah']->no_hp,
                    'nomor_rw' => $result['nasabah']->nomor_rw,
                    'nomor_rt' => $result['nasabah']->nomor_rt,
                    'alamat' => $result['nasabah']->alamat,
                    'saldo_aktif' => (float) $result['nasabah']->saldo_aktif,
                    'created_by' => $kader->name,
                    'created_at' => $result['nasabah']->created_at,
                ],
                'credentials' => [
                    'username' => $username,
                    'initial_password' => $plainPassword,
                    'info' => 'Berikan username dan initial_password ini kepada nasabah untuk login mandiri.',
                ],
            ],
        ], 201);
    }

    /**
     * Menampilkan daftar nasabah dengan fitur pencarian dan filter RW/RT.
     */
    public function listNasabah(Request $request): JsonResponse
    {
        $query = Nasabah::with(['user:id,username,email,is_active', 'kaderPendaftar:id,name']);

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

        $nasabahList = $query->orderBy('nama', 'asc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $nasabahList,
        ], 200);
    }

    /**
     * Menampilkan detail satu nasabah beserta ringkasan saldo.
     */
    public function showNasabah(int $id): JsonResponse
    {
        $nasabah = Nasabah::with([
            'user:id,username,email,is_active',
            'kaderPendaftar:id,name',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $nasabah,
        ], 200);
    }

    /**
     * Memperbarui data profil nasabah dan akun login user terkait oleh Kader.
     */
    public function updateNasabah(UpdateNasabahRequest $request, int $id): JsonResponse
    {
        $nasabah = Nasabah::findOrFail($id);
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $nasabah, $request) {
            $nasabah->update([
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'nomor_rw' => $validated['nomor_rw'],
                'nomor_rt' => $validated['nomor_rt'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
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
     * Menghapus nasabah dan akun login user terkait oleh Kader.
     */
    public function deleteNasabah(int $id): JsonResponse
    {
        $nasabah = Nasabah::findOrFail($id);

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
