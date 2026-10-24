<?php

use App\Http\Controllers\Api\Admin\NasabahController as ApiAdminNasabahController;
use App\Http\Controllers\Api\Admin\UserController as ApiAdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaderController;
use App\Http\Controllers\KategoriSampahController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PenarikanDanaController;
use App\Http\Controllers\TransaksiSetorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// Master Kategori Sampah (dapat dilihat publik/terautentikasi)
Route::get('/kategori-sampah', [KategoriSampahController::class, 'index'])->name('kategori-sampah.index');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Info akun & logout
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });

    /*
    |--------------------------------------------------------------------------
    | Endpoint Khusus Admin (CRUD User & Nasabah)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('api.admin.')->group(function () {
        Route::apiResource('users', ApiAdminUserController::class);
        Route::apiResource('nasabah', ApiAdminNasabahController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Endpoint Khusus Kader & Admin
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:kader,admin')->prefix('kader')->name('api.kader.')->group(function () {
        // 1. Pendaftaran & Manajemen Nasabah
        Route::post('/nasabah', [KaderController::class, 'registerNasabah'])->name('nasabah.register');
        Route::get('/nasabah', [KaderController::class, 'listNasabah'])->name('nasabah.index');
        Route::get('/nasabah/{id}', [KaderController::class, 'showNasabah'])->name('nasabah.show');
        Route::put('/nasabah/{id}', [KaderController::class, 'updateNasabah'])->name('nasabah.update');
        Route::delete('/nasabah/{id}', [KaderController::class, 'deleteNasabah'])->name('nasabah.destroy');

        // 2. Input & Manajemen Transaksi Setoran Sampah
        Route::post('/setor', [TransaksiSetorController::class, 'store'])->name('setor.store');
        Route::get('/setor', [TransaksiSetorController::class, 'index'])->name('setor.index');
        Route::get('/setor/{id}', [TransaksiSetorController::class, 'show'])->name('setor.show');

        // 3. Manajemen & Validasi Penarikan Dana
        Route::get('/penarikan', [PenarikanDanaController::class, 'index'])->name('penarikan.index');
        Route::get('/penarikan/{id}', [PenarikanDanaController::class, 'show'])->name('penarikan.show');
        Route::post('/penarikan/{id}/validasi', [PenarikanDanaController::class, 'validasi'])->name('penarikan.validasi');
    });

    /*
    |--------------------------------------------------------------------------
    | Endpoint Khusus Nasabah
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:nasabah')->prefix('nasabah')->name('nasabah.')->group(function () {
        // Cek profil, saldo mandiri, dan riwayat mutasi
        Route::get('/profil', [NasabahController::class, 'profil'])->name('profil');
        Route::get('/saldo', [NasabahController::class, 'saldo'])->name('saldo');
        Route::get('/mutasi', [NasabahController::class, 'mutasi'])->name('mutasi');
        Route::get('/setor', [NasabahController::class, 'riwayatSetor'])->name('riwayat-setor');
        Route::get('/penarikan', [NasabahController::class, 'riwayatPenarikan'])->name('riwayat-penarikan');

        // Pengajuan penarikan dana
        Route::post('/penarikan', [PenarikanDanaController::class, 'ajukan'])->name('penarikan.ajukan');
    });
});
