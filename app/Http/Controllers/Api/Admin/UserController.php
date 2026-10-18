<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user dengan pencarian dan filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->with('nasabah')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users,
        ], 200);
    }

    /**
     * Membuat akun user baru oleh Admin.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User baru berhasil dibuat.',
            'data' => $user,
        ], 201);
    }

    /**
     * Menampilkan detail satu user.
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['nasabah', 'nasabahDidaftarkan'])
            ->loadCount(['transaksiSetorDicatat', 'penarikanDiproses']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 200);
    }

    /**
     * Memperbarui data akun user.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        if ($user->id === $request->user()->id) {
            if ($validated['role'] !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat mengubah role akun admin Anda sendiri.',
                ], 422);
            }

            if (! $request->boolean('is_active', true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menonaktifkan akun admin Anda sendiri.',
                ], 422);
            }
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diperbarui.',
            'data' => $user,
        ], 200);
    }

    /**
     * Menghapus user dari sistem.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.',
        ], 200);
    }
}
