<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Otentikasi login untuk Kader dan Nasabah.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $loginInput = $request->validated('login');
        $password = $request->validated('password');

        // Pencarian user berdasarkan username, email, atau NIK nasabah
        $user = User::where('username', $loginInput)
            ->orWhere('email', $loginInput)
            ->first();

        if (! $user) {
            $nasabah = Nasabah::where('nik', $loginInput)->first();
            if ($nasabah) {
                $user = $nasabah->user;
            }
        }

        if (! $user || ! Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial login tidak valid. Periksa username/NIK dan password Anda.',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda berstatus non-aktif. Silakan hubungi Kader atau Admin.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->load('nasabah');

        return response()->json([
            'success' => true,
            'message' => "Selamat datang, {$user->name}!",
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'nasabah' => $user->nasabah ? [
                        'id' => $user->nasabah->id,
                        'nik' => $user->nasabah->nik,
                        'nama' => $user->nasabah->nama,
                        'no_hp' => $user->nasabah->no_hp,
                        'nomor_rw' => $user->nasabah->nomor_rw,
                        'nomor_rt' => $user->nasabah->nomor_rt,
                        'alamat' => $user->nasabah->alamat,
                        'saldo_aktif' => (float) $user->nasabah->saldo_aktif,
                    ] : null,
                ],
            ],
        ], 200);
    }

    /**
     * Dapatkan informasi user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('nasabah');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'nasabah' => $user->nasabah ? [
                    'id' => $user->nasabah->id,
                    'nik' => $user->nasabah->nik,
                    'nama' => $user->nasabah->nama,
                    'no_hp' => $user->nasabah->no_hp,
                    'nomor_rw' => $user->nasabah->nomor_rw,
                    'nomor_rt' => $user->nasabah->nomor_rt,
                    'alamat' => $user->nasabah->alamat,
                    'saldo_aktif' => (float) $user->nasabah->saldo_aktif,
                ] : null,
            ],
        ], 200);
    }

    /**
     * Logout dan revokasi token akses saat ini.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout.',
        ], 200);
    }
}
