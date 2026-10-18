<?php

namespace Database\Seeders;

use App\Models\DetailTransaksiSetor;
use App\Models\KategoriSampah;
use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use App\Models\TransaksiTarikDana;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransaksiDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kaderSiti = User::where('username', 'kader_siti')->first();
        $kaderBudi = User::where('username', 'kader_budi')->first();

        $nasabahDewi = Nasabah::where('nik', '3201015502900001')->first();
        $nasabahAgus = Nasabah::where('nik', '3201011208880002')->first();
        $nasabahBambang = Nasabah::where('nik', '3201011005850004')->first();
        $nasabahEko = Nasabah::where('nik', '3201012304890006')->first();
        $nasabahMaya = Nasabah::where('nik', '3201015607940007')->first();

        $kardus = KategoriSampah::where('kode', '3F')->orWhere('nama_kategori', 'like', '%Kardus%')->first();
        $botolPET = KategoriSampah::where('kode', '2D')->orWhere('nama_kategori', 'like', '%Botol Mineral%')->first();
        $gelasPP = KategoriSampah::where('kode', '2K')->orWhere('nama_kategori', 'like', '%Gelas%')->first();
        $logamBesi = KategoriSampah::where('kode', '1B')->orWhere('nama_kategori', 'like', '%Besi%')->first();
        $kalengAluminium = KategoriSampah::where('kode', '1D')->orWhere('kode', '1A')->orWhere('nama_kategori', 'like', '%Kaleng%')->first();
        $kertasHVS = KategoriSampah::where('kode', '3I')->orWhere('nama_kategori', 'like', '%HVS%')->first();
        $kertasKoran = KategoriSampah::where('kode', '3E')->orWhere('nama_kategori', 'like', '%Koran%')->first();
        $minyakJelantah = KategoriSampah::where('kode', '7D')->orWhere('nama_kategori', 'like', '%Jelantah%')->first();

        // ==========================================
        // 1. TRANSAKSI SETOR 1 (Dewi Lestari)
        // ==========================================
        if ($nasabahDewi && ! TransaksiSetor::where('kode_transaksi', 'SETOR-20261001-0001')->exists()) {
            $tgl = Carbon::parse('2026-10-01 09:30:00');
            $trx1 = TransaksiSetor::create([
                'kode_transaksi' => 'SETOR-20261001-0001',
                'nasabah_id' => $nasabahDewi->id,
                'kader_id' => $kaderSiti->id,
                'total_berat' => 15.00,
                'total_nominal' => 40000.00,
                'tanggal_setor' => $tgl,
                'catatan' => 'Setoran kardus dan botol PET bersih',
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx1->id,
                'kategori_sampah_id' => $kardus->id,
                'berat' => 10.00,
                'harga_satuan' => $kardus->harga_per_kg,
                'subtotal' => 25000.00,
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx1->id,
                'kategori_sampah_id' => $botolPET->id,
                'berat' => 5.00,
                'harga_satuan' => $botolPET->harga_per_kg,
                'subtotal' => 15000.00,
            ]);

            MutasiSaldo::create([
                'nasabah_id' => $nasabahDewi->id,
                'jenis_mutasi' => 'masuk',
                'nominal' => 40000.00,
                'saldo_sebelum' => 0.00,
                'saldo_sesudah' => 40000.00,
                'referensi_id' => $trx1->id,
                'tipe_referensi' => 'transaksi_setor',
                'deskripsi' => 'Setoran sampah #SETOR-20261001-0001 (15.00 kg)',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);

            $nasabahDewi->update(['saldo_aktif' => 40000.00]);
        }

        // ==========================================
        // 2. TRANSAKSI SETOR 2 (Dewi Lestari)
        // ==========================================
        if ($nasabahDewi && ! TransaksiSetor::where('kode_transaksi', 'SETOR-20261005-0002')->exists()) {
            $tgl = Carbon::parse('2026-10-05 10:15:00');
            $trx2 = TransaksiSetor::create([
                'kode_transaksi' => 'SETOR-20261005-0002',
                'nasabah_id' => $nasabahDewi->id,
                'kader_id' => $kaderSiti->id,
                'total_berat' => 8.00,
                'total_nominal' => 36000.00,
                'tanggal_setor' => $tgl,
                'catatan' => 'Setoran rongsokan besi pagar bekas',
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx2->id,
                'kategori_sampah_id' => $logamBesi->id,
                'berat' => 8.00,
                'harga_satuan' => $logamBesi->harga_per_kg,
                'subtotal' => 36000.00,
            ]);

            $saldoSebelum = $nasabahDewi->saldo_aktif;
            $saldoSesudah = $saldoSebelum + 36000.00;

            MutasiSaldo::create([
                'nasabah_id' => $nasabahDewi->id,
                'jenis_mutasi' => 'masuk',
                'nominal' => 36000.00,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'referensi_id' => $trx2->id,
                'tipe_referensi' => 'transaksi_setor',
                'deskripsi' => 'Setoran sampah #SETOR-20261005-0002 (8.00 kg)',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);

            $nasabahDewi->update(['saldo_aktif' => $saldoSesudah]);
        }

        // ==========================================
        // 3. TRANSAKSI TARIK DANA 1 - Selesai (Dewi Lestari)
        // ==========================================
        if ($nasabahDewi && ! TransaksiTarikDana::where('kode_penarikan', 'TARIK-20261008-0001')->exists()) {
            $tgl = Carbon::parse('2026-10-08 14:00:00');
            $tarik1 = TransaksiTarikDana::create([
                'kode_penarikan' => 'TARIK-20261008-0001',
                'nasabah_id' => $nasabahDewi->id,
                'jumlah_penarikan' => 25000.00,
                'metode_penarikan' => 'tunai',
                'status' => 'selesai',
                'diproses_oleh' => $kaderSiti->id,
                'tanggal_tarik' => $tgl,
                'catatan' => 'Penarikan tunai diserahkan di pos RW 05',
            ]);

            $saldoSebelum = $nasabahDewi->saldo_aktif;
            $saldoSesudah = $saldoSebelum - 25000.00;

            MutasiSaldo::create([
                'nasabah_id' => $nasabahDewi->id,
                'jenis_mutasi' => 'keluar',
                'nominal' => 25000.00,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'referensi_id' => $tarik1->id,
                'tipe_referensi' => 'transaksi_tarik_dana',
                'deskripsi' => 'Penarikan dana tunai #TARIK-20261008-0001',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);

            $nasabahDewi->update(['saldo_aktif' => $saldoSesudah]);
        }

        // ==========================================
        // 4. TRANSAKSI SETOR 3 (Agus Pratama)
        // ==========================================
        if ($nasabahAgus && ! TransaksiSetor::where('kode_transaksi', 'SETOR-20261007-0003')->exists()) {
            $tgl = Carbon::parse('2026-10-07 11:00:00');
            $trx3 = TransaksiSetor::create([
                'kode_transaksi' => 'SETOR-20261007-0003',
                'nasabah_id' => $nasabahAgus->id,
                'kader_id' => $kaderSiti->id,
                'total_berat' => 16.00,
                'total_nominal' => 58000.00,
                'tanggal_setor' => $tgl,
                'catatan' => 'Minyak jelantah jerigen dan kertas bekas HVS',
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx3->id,
                'kategori_sampah_id' => $minyakJelantah->id,
                'berat' => 6.00,
                'harga_satuan' => $minyakJelantah->harga_per_kg,
                'subtotal' => 36000.00,
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx3->id,
                'kategori_sampah_id' => $kertasHVS->id,
                'berat' => 10.00,
                'harga_satuan' => $kertasHVS->harga_per_kg,
                'subtotal' => 22000.00,
            ]);

            MutasiSaldo::create([
                'nasabah_id' => $nasabahAgus->id,
                'jenis_mutasi' => 'masuk',
                'nominal' => 58000.00,
                'saldo_sebelum' => 0.00,
                'saldo_sesudah' => 58000.00,
                'referensi_id' => $trx3->id,
                'tipe_referensi' => 'transaksi_setor',
                'deskripsi' => 'Setoran sampah #SETOR-20261007-0003 (16.00 kg)',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);

            $nasabahAgus->update(['saldo_aktif' => 58000.00]);
        }

        // ==========================================
        // 5. TRANSAKSI TARIK DANA 2 - Pending (Agus Pratama)
        // ==========================================
        if ($nasabahAgus && ! TransaksiTarikDana::where('kode_penarikan', 'TARIK-20261009-0002')->exists()) {
            TransaksiTarikDana::create([
                'kode_penarikan' => 'TARIK-20261009-0002',
                'nasabah_id' => $nasabahAgus->id,
                'jumlah_penarikan' => 20000.00,
                'metode_penarikan' => 'transfer',
                'status' => 'pending',
                'diproses_oleh' => null,
                'tanggal_tarik' => Carbon::parse('2026-10-09 16:30:00'),
                'catatan' => 'Mohon transfer ke BCA Rek. 1234567890 an Agus Pratama',
            ]);
        }

        // ==========================================
        // 6. TRANSAKSI SETOR 4 (Bambang Pamungkas)
        // ==========================================
        if ($nasabahBambang && ! TransaksiSetor::where('kode_transaksi', 'SETOR-20261008-0004')->exists()) {
            $tgl = Carbon::parse('2026-10-08 10:00:00');
            $trx4 = TransaksiSetor::create([
                'kode_transaksi' => 'SETOR-20261008-0004',
                'nasabah_id' => $nasabahBambang->id,
                'kader_id' => $kaderSiti->id,
                'total_berat' => 8.00,
                'total_nominal' => 46000.00,
                'tanggal_setor' => $tgl,
                'catatan' => 'Kaleng minuman dan gelas plastik',
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx4->id,
                'kategori_sampah_id' => $kalengAluminium->id,
                'berat' => 3.00,
                'harga_satuan' => $kalengAluminium->harga_per_kg,
                'subtotal' => 36000.00,
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx4->id,
                'kategori_sampah_id' => $gelasPP->id,
                'berat' => 5.00,
                'harga_satuan' => $gelasPP->harga_per_kg,
                'subtotal' => 10000.00,
            ]);

            MutasiSaldo::create([
                'nasabah_id' => $nasabahBambang->id,
                'jenis_mutasi' => 'masuk',
                'nominal' => 46000.00,
                'saldo_sebelum' => 0.00,
                'saldo_sesudah' => 46000.00,
                'referensi_id' => $trx4->id,
                'tipe_referensi' => 'transaksi_setor',
                'deskripsi' => 'Setoran sampah #SETOR-20261008-0004 (8.00 kg)',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);

            $nasabahBambang->update(['saldo_aktif' => 46000.00]);
        }

        // ==========================================
        // 7. TRANSAKSI TARIK DANA 3 - Selesai (Bambang Pamungkas)
        // ==========================================
        if ($nasabahBambang && ! TransaksiTarikDana::where('kode_penarikan', 'TARIK-20261009-0003')->exists()) {
            $tgl = Carbon::parse('2026-10-09 11:20:00');
            $tarik3 = TransaksiTarikDana::create([
                'kode_penarikan' => 'TARIK-20261009-0003',
                'nasabah_id' => $nasabahBambang->id,
                'jumlah_penarikan' => 30000.00,
                'metode_penarikan' => 'transfer',
                'status' => 'selesai',
                'diproses_oleh' => $kaderSiti->id,
                'tanggal_tarik' => $tgl,
                'catatan' => 'Transfer berhasil via Bank Mandiri No. 987654321',
            ]);

            $saldoSebelum = $nasabahBambang->saldo_aktif;
            $saldoSesudah = $saldoSebelum - 30000.00;

            MutasiSaldo::create([
                'nasabah_id' => $nasabahBambang->id,
                'jenis_mutasi' => 'keluar',
                'nominal' => 30000.00,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'referensi_id' => $tarik3->id,
                'tipe_referensi' => 'transaksi_tarik_dana',
                'deskripsi' => 'Penarikan dana transfer #TARIK-20261009-0003',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);

            $nasabahBambang->update(['saldo_aktif' => $saldoSesudah]);
        }

        // ==========================================
        // 8. TRANSAKSI SETOR 5 (Eko Prasetyo)
        // ==========================================
        if ($nasabahEko && ! TransaksiSetor::where('kode_transaksi', 'SETOR-20261009-0005')->exists()) {
            $tgl = Carbon::parse('2026-10-09 13:45:00');
            $trx5 = TransaksiSetor::create([
                'kode_transaksi' => 'SETOR-20261009-0005',
                'nasabah_id' => $nasabahEko->id,
                'kader_id' => $kaderBudi->id,
                'total_berat' => 30.00,
                'total_nominal' => 68000.00,
                'tanggal_setor' => $tgl,
                'catatan' => 'Kardus tebal dan koran bekas langganan kantor',
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx5->id,
                'kategori_sampah_id' => $kardus->id,
                'berat' => 20.00,
                'harga_satuan' => $kardus->harga_per_kg,
                'subtotal' => 50000.00,
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx5->id,
                'kategori_sampah_id' => $kertasKoran->id,
                'berat' => 10.00,
                'harga_satuan' => $kertasKoran->harga_per_kg,
                'subtotal' => 18000.00,
            ]);

            MutasiSaldo::create([
                'nasabah_id' => $nasabahEko->id,
                'jenis_mutasi' => 'masuk',
                'nominal' => 68000.00,
                'saldo_sebelum' => 0.00,
                'saldo_sesudah' => 68000.00,
                'referensi_id' => $trx5->id,
                'tipe_referensi' => 'transaksi_setor',
                'deskripsi' => 'Setoran sampah #SETOR-20261009-0005 (30.00 kg)',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);

            $nasabahEko->update(['saldo_aktif' => 68000.00]);
        }

        // ==========================================
        // 9. TRANSAKSI TARIK DANA 4 - Ditolak (Eko Prasetyo)
        // ==========================================
        if ($nasabahEko && ! TransaksiTarikDana::where('kode_penarikan', 'TARIK-20261010-0004')->exists()) {
            TransaksiTarikDana::create([
                'kode_penarikan' => 'TARIK-20261010-0004',
                'nasabah_id' => $nasabahEko->id,
                'jumlah_penarikan' => 50000.00,
                'metode_penarikan' => 'tunai',
                'status' => 'ditolak',
                'diproses_oleh' => $kaderBudi->id,
                'tanggal_tarik' => Carbon::parse('2026-10-10 15:00:00'),
                'catatan' => 'Kas tunai pos ronda sedang habis, mohon ajukan metode transfer atau minggu depan',
            ]);
            // Saldo Eko tidak berkurang (tetap 68.000) dan mutasi saldo keluar tidak dibuat
        }

        // ==========================================
        // 10. TRANSAKSI SETOR 6 (Maya Indah)
        // ==========================================
        if ($nasabahMaya && ! TransaksiSetor::where('kode_transaksi', 'SETOR-20261010-0006')->exists()) {
            $tgl = Carbon::parse('2026-10-10 14:15:00');
            $trx6 = TransaksiSetor::create([
                'kode_transaksi' => 'SETOR-20261010-0006',
                'nasabah_id' => $nasabahMaya->id,
                'kader_id' => $kaderBudi->id,
                'total_berat' => 12.00,
                'total_nominal' => 48000.00,
                'tanggal_setor' => $tgl,
                'catatan' => 'Botol plastik dan minyak jelantah sisa dapur',
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx6->id,
                'kategori_sampah_id' => $botolPET->id,
                'berat' => 8.00,
                'harga_satuan' => $botolPET->harga_per_kg,
                'subtotal' => 24000.00,
            ]);

            DetailTransaksiSetor::create([
                'transaksi_setor_id' => $trx6->id,
                'kategori_sampah_id' => $minyakJelantah->id,
                'berat' => 4.00,
                'harga_satuan' => $minyakJelantah->harga_per_kg,
                'subtotal' => 24000.00,
            ]);

            MutasiSaldo::create([
                'nasabah_id' => $nasabahMaya->id,
                'jenis_mutasi' => 'masuk',
                'nominal' => 48000.00,
                'saldo_sebelum' => 0.00,
                'saldo_sesudah' => 48000.00,
                'referensi_id' => $trx6->id,
                'tipe_referensi' => 'transaksi_setor',
                'deskripsi' => 'Setoran sampah #SETOR-20261010-0006 (12.00 kg)',
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);

            $nasabahMaya->update(['saldo_aktif' => 48000.00]);
        }
    }
}
