<?php

use App\Http\Controllers\Admin\KategoriSampahController as AdminKategoriSampahController;
use App\Http\Controllers\Admin\NasabahController as AdminNasabahController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaderNasabahWebController;
use App\Http\Controllers\KatalogSampahWebController;
use App\Http\Controllers\NasabahWargaWebController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetorSampahController;
use App\Http\Controllers\TarikDanaWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/kategori-sampah-warga', [KatalogSampahWebController::class, 'index'])->name('katalog-sampah.index');
});

Route::middleware(['auth', 'role:nasabah'])->group(function () {
    Route::post('/penarikan-dana/ajukan', [TarikDanaWebController::class, 'ajukan'])->name('penarikan-dana.ajukan');
    Route::get('/data-nasabah-warga', [NasabahWargaWebController::class, 'index'])->name('nasabah.warga.index');
});

Route::middleware(['auth', 'role:kader,admin'])->group(function () {
    Route::resource('setor-sampah', SetorSampahController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/penarikan-dana', [TarikDanaWebController::class, 'index'])->name('penarikan-dana.index');
    Route::get('/penarikan-dana/{penarikan}', [TarikDanaWebController::class, 'show'])->name('penarikan-dana.show');
    Route::post('/penarikan-dana/{penarikan}/validasi', [TarikDanaWebController::class, 'validasi'])->name('penarikan-dana.validasi');

    Route::get('/nasabah', [KaderNasabahWebController::class, 'index'])->name('kader.nasabah.index');
    Route::get('/nasabah/tambah', [KaderNasabahWebController::class, 'create'])->name('kader.nasabah.create');
    Route::post('/nasabah', [KaderNasabahWebController::class, 'store'])->name('kader.nasabah.store');
    Route::get('/nasabah/{nasabah}', [KaderNasabahWebController::class, 'show'])->name('kader.nasabah.show');
    Route::get('/nasabah/{nasabah}/edit', [KaderNasabahWebController::class, 'edit'])->name('kader.nasabah.edit');
    Route::put('/nasabah/{nasabah}', [KaderNasabahWebController::class, 'update'])->name('kader.nasabah.update');
    Route::delete('/nasabah/{nasabah}', [KaderNasabahWebController::class, 'destroy'])->name('kader.nasabah.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminUserController::class);
    Route::resource('nasabah', AdminNasabahController::class);
    Route::resource('kategori-sampah', AdminKategoriSampahController::class);
    Route::post('kategori-sampah/{kategori_sampah}/toggle-status', [AdminKategoriSampahController::class, 'toggleStatus'])
        ->name('kategori-sampah.toggle-status');
});

require __DIR__.'/auth.php';
