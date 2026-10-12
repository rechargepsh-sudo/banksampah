<?php

use App\Models\DetailTransaksiSetor;
use App\Models\KategoriSampah;
use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use App\Models\TransaksiTarikDana;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:check-stats', function () {
    $this->info('==========================================');
    $this->info('1. JUMLAH DATA PER MODEL (ACTUAL COUNT)');
    $this->info('==========================================');

    $models = [
        'User' => User::class,
        'Nasabah' => Nasabah::class,
        'KategoriSampah' => KategoriSampah::class,
        'TransaksiSetor' => TransaksiSetor::class,
        'DetailTransaksiSetor' => DetailTransaksiSetor::class,
        'TransaksiTarikDana' => TransaksiTarikDana::class,
        'MutasiSaldo' => MutasiSaldo::class,
    ];

    foreach ($models as $name => $class) {
        $this->line("{$name}: ".$class::count());
    }

    $this->newLine();
    $this->info('==========================================');
    $this->info('2. CONTOH RECORD TIAP MODEL');
    $this->info('==========================================');

    // Contoh User & Nasabah
    $user = User::with('nasabah')->where('role', 'nasabah')->first();
    $this->comment("Contoh User Nasabah (ID: {$user->id}):");
    $this->line(" - Username: {$user->username} | Email: {$user->email} | Role: {$user->role}");
    $this->line(" - Profil Nasabah Terkait: NIK {$user->nasabah->nik}, Nama {$user->nasabah->nama}, Saldo Aktif Rp ".number_format($user->nasabah->saldo_aktif, 0, ',', '.'));

    // Contoh Kategori Sampah
    $kategori = KategoriSampah::first();
    $this->comment("Contoh Kategori Sampah (ID: {$kategori->id}):");
    $this->line(" - Nama: {$kategori->nama_kategori} | Harga: Rp ".number_format($kategori->harga_per_kg, 0, ',', '.')." / {$kategori->satuan} | Aktif: ".($kategori->is_active ? 'Ya' : 'Tidak'));

    // Contoh Transaksi Setor & Detail
    $setor = TransaksiSetor::with(['nasabah', 'kader', 'details.kategoriSampah'])->first();
    $this->comment("Contoh Transaksi Setor (Kode: {$setor->kode_transaksi}):");
    $this->line(" - Nasabah: {$setor->nasabah->nama} | Kader: {$setor->kader->name}");
    $this->line(" - Total Berat: {$setor->total_berat} kg | Total Nominal: Rp ".number_format($setor->total_nominal, 0, ',', '.'));
    $this->line(' - Rincian Detail ('.$setor->details->count().' item):');
    foreach ($setor->details as $d) {
        $this->line("   * {$d->kategoriSampah->nama_kategori}: {$d->berat} kg @ Rp ".number_format($d->harga_satuan, 0, ',', '.').' = Rp '.number_format($d->subtotal, 0, ',', '.'));
    }

    // Contoh Transaksi Tarik Dana
    $tarik = TransaksiTarikDana::with(['nasabah', 'diprosesOleh'])->first();
    $this->comment("Contoh Transaksi Tarik Dana (Kode: {$tarik->kode_penarikan}):");
    $this->line(" - Nasabah: {$tarik->nasabah->nama} | Jumlah: Rp ".number_format($tarik->jumlah_penarikan, 0, ',', '.'));
    $this->line(" - Metode: {$tarik->metode_penarikan} | Status: {$tarik->status} | Diproses Oleh: ".($tarik->diprosesOleh?->name ?? 'Belum diproses'));

    // Contoh Mutasi Saldo
    $mutasi = MutasiSaldo::with('nasabah')->first();
    $this->comment("Contoh Mutasi Saldo (ID: {$mutasi->id}):");
    $this->line(" - Nasabah: {$mutasi->nasabah->nama} | Jenis: {$mutasi->jenis_mutasi} | Nominal: Rp ".number_format($mutasi->nominal, 0, ',', '.'));
    $this->line(' - Saldo Sebelum: Rp '.number_format($mutasi->saldo_sebelum, 0, ',', '.').' -> Saldo Sesudah: Rp '.number_format($mutasi->saldo_sesudah, 0, ',', '.'));
    $this->line(" - Referensi: {$mutasi->tipe_referensi} #{$mutasi->referensi_id} | Deskripsi: {$mutasi->deskripsi}");

    $this->newLine();
    $this->info('==========================================');
    $this->info('3. VALIDASI INTEGRITAS RELASI');
    $this->info('==========================================');

    // Relasi 1: User -> Nasabah
    $usersWithNasabah = User::where('role', 'nasabah')->has('nasabah')->count();
    $totalNasabahUsers = User::where('role', 'nasabah')->count();
    $this->line("Relasi User -> Nasabah: {$usersWithNasabah}/{$totalNasabahUsers} valid (".($usersWithNasabah === $totalNasabahUsers ? 'OK' : 'MISMATCH').')');

    // Relasi 2: Nasabah -> User & Kader Pendaftar
    $nasabahValidUser = Nasabah::whereHas('user')->count();
    $nasabahValidKader = Nasabah::whereHas('kaderPendaftar')->count();
    $totalNasabah = Nasabah::count();
    $this->line("Relasi Nasabah -> User: {$nasabahValidUser}/{$totalNasabah} valid (".($nasabahValidUser === $totalNasabah ? 'OK' : 'MISMATCH').')');
    $this->line("Relasi Nasabah -> Kader Pendaftar: {$nasabahValidKader}/{$totalNasabah} valid (".($nasabahValidKader === $totalNasabah ? 'OK' : 'MISMATCH').')');

    // Relasi 3: TransaksiSetor -> Nasabah, Kader & Details
    $setorWithNasabah = TransaksiSetor::whereHas('nasabah')->count();
    $setorWithKader = TransaksiSetor::whereHas('kader')->count();
    $setorWithDetails = TransaksiSetor::has('details')->count();
    $totalSetor = TransaksiSetor::count();
    $this->line("Relasi TransaksiSetor -> Nasabah: {$setorWithNasabah}/{$totalSetor} valid (".($setorWithNasabah === $totalSetor ? 'OK' : 'MISMATCH').')');
    $this->line("Relasi TransaksiSetor -> Kader: {$setorWithKader}/{$totalSetor} valid (".($setorWithKader === $totalSetor ? 'OK' : 'MISMATCH').')');
    $this->line("Relasi TransaksiSetor -> Details: {$setorWithDetails}/{$totalSetor} valid (".($setorWithDetails === $totalSetor ? 'OK' : 'MISMATCH').')');

    // Relasi 4: DetailTransaksiSetor -> KategoriSampah
    $detailWithKategori = DetailTransaksiSetor::whereHas('kategoriSampah')->count();
    $totalDetails = DetailTransaksiSetor::count();
    $this->line("Relasi Detail -> KategoriSampah: {$detailWithKategori}/{$totalDetails} valid (".($detailWithKategori === $totalDetails ? 'OK' : 'MISMATCH').')');

    // Relasi 5: TransaksiTarikDana -> Nasabah
    $tarikWithNasabah = TransaksiTarikDana::whereHas('nasabah')->count();
    $totalTarik = TransaksiTarikDana::count();
    $this->line("Relasi TransaksiTarikDana -> Nasabah: {$tarikWithNasabah}/{$totalTarik} valid (".($tarikWithNasabah === $totalTarik ? 'OK' : 'MISMATCH').')');

    // Relasi 6: MutasiSaldo -> Nasabah
    $mutasiWithNasabah = MutasiSaldo::whereHas('nasabah')->count();
    $totalMutasi = MutasiSaldo::count();
    $this->line("Relasi MutasiSaldo -> Nasabah: {$mutasiWithNasabah}/{$totalMutasi} valid (".($mutasiWithNasabah === $totalMutasi ? 'OK' : 'MISMATCH').')');
});
