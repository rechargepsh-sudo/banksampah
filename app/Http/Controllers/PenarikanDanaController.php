<?php

namespace App\Http\Controllers;

use App\Http\Requests\Penarikan\AjukanPenarikanRequest;
use App\Http\Requests\Penarikan\ValidasiPenarikanRequest;
use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use App\Models\TransaksiTarikDana;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenarikanDanaController extends Controller
{
    /**
     * Pengajuan permohonan penarikan dana oleh Nasabah.
     */
    public function ajukan(AjukanPenarikanRequest $request): JsonResponse
    {
        $nasabah = $request->user()->nasabah;

        if (! $nasabah) {
            return response()->json([
                'success' => false,
                'message' => 'Profil nasabah tidak ditemukan.',
            ], 404);
        }

        $jumlahPenarikan = (float) $request->validated('jumlah_penarikan');

        // Validasi ketersediaan saldo aktif
        if ($nasabah->saldo_aktif < $jumlahPenarikan) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi untuk melakukan penarikan.',
                'saldo_aktif_saat_ini' => (float) $nasabah->saldo_aktif,
                'jumlah_diajukan' => $jumlahPenarikan,
            ], 422);
        }

        // Generate kode penarikan unik
        $dateStr = now()->format('Ymd');
        $countToday = TransaksiTarikDana::whereDate('created_at', today())->count() + 1;
        $kodePenarikan = "TARIK-{$dateStr}-".str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
        while (TransaksiTarikDana::where('kode_penarikan', $kodePenarikan)->exists()) {
            $countToday++;
            $kodePenarikan = "TARIK-{$dateStr}-".str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
        }

        $penarikan = TransaksiTarikDana::create([
            'kode_penarikan' => $kodePenarikan,
            'nasabah_id' => $nasabah->id,
            'jumlah_penarikan' => $jumlahPenarikan,
            'metode_penarikan' => $request->validated('metode_penarikan'),
            'status' => 'pending',
            'diproses_oleh' => null,
            'tanggal_tarik' => now(),
            'catatan' => $request->validated('catatan'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan penarikan dana berhasil diajukan dan sedang menunggu validasi Kader.',
            'data' => [
                'id' => $penarikan->id,
                'kode_penarikan' => $penarikan->kode_penarikan,
                'jumlah_penarikan' => (float) $penarikan->jumlah_penarikan,
                'metode_penarikan' => $penarikan->metode_penarikan,
                'status' => $penarikan->status,
                'tanggal_tarik' => $penarikan->tanggal_tarik->format('Y-m-d H:i:s'),
                'catatan' => $penarikan->catatan,
                'saldo_aktif_nasabah' => (float) $nasabah->saldo_aktif,
            ],
        ], 201);
    }

    /**
     * Validasi permohonan penarikan dana oleh Kader (disetujui/ditolak).
     * Jika disetujui ('selesai'), saldo_aktif otomatis dipotong dan mutasi saldo dicatat via DB transaction.
     */
    public function validasi(ValidasiPenarikanRequest $request, int $id): JsonResponse
    {
        $kader = $request->user();
        $targetStatus = $request->validated('status');
        $catatanTambahan = $request->validated('catatan');

        $result = DB::transaction(function () use ($id, $targetStatus, $catatanTambahan, $kader) {
            $penarikan = TransaksiTarikDana::lockForUpdate()->findOrFail($id);

            if ($penarikan->status !== 'pending') {
                return [
                    'status_error' => 400,
                    'message' => "Permohonan penarikan dana ini sudah diproses sebelumnya dengan status [{$penarikan->status}].",
                ];
            }

            $nasabah = Nasabah::lockForUpdate()->findOrFail($penarikan->nasabah_id);

            if ($targetStatus === 'selesai') {
                $jumlah = (float) $penarikan->jumlah_penarikan;
                $saldoSebelum = (float) $nasabah->saldo_aktif;

                // Verifikasi ulang kecukupan saldo sebelum dipotong
                if ($saldoSebelum < $jumlah) {
                    return [
                        'status_error' => 422,
                        'message' => 'Saldo aktif nasabah (Rp '.number_format($saldoSebelum, 0, ',', '.').') tidak mencukupi untuk menyelesaikan penarikan dana sejumlah Rp '.number_format($jumlah, 0, ',', '.').'.',
                    ];
                }

                $saldoSesudah = round($saldoSebelum - $jumlah, 2);

                // Catat mutasi saldo keluar
                MutasiSaldo::create([
                    'nasabah_id' => $nasabah->id,
                    'jenis_mutasi' => 'keluar',
                    'nominal' => $jumlah,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                    'referensi_id' => $penarikan->id,
                    'tipe_referensi' => 'transaksi_tarik_dana',
                    'deskripsi' => "Penarikan dana ({$penarikan->metode_penarikan}) #{$penarikan->kode_penarikan}",
                ]);

                // Potong saldo aktif nasabah
                $nasabah->update([
                    'saldo_aktif' => $saldoSesudah,
                ]);

                // Update status penarikan
                $penarikan->update([
                    'status' => 'selesai',
                    'diproses_oleh' => $kader->id,
                    'catatan' => $catatanTambahan ?? $penarikan->catatan,
                ]);

                return [
                    'penarikan' => $penarikan,
                    'nasabah' => $nasabah,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                ];
            }

            // Jika status ditolak
            $penarikan->update([
                'status' => 'ditolak',
                'diproses_oleh' => $kader->id,
                'catatan' => $catatanTambahan ?? $penarikan->catatan,
            ]);

            return [
                'penarikan' => $penarikan,
                'nasabah' => $nasabah,
                'saldo_sebelum' => (float) $nasabah->saldo_aktif,
                'saldo_sesudah' => (float) $nasabah->saldo_aktif,
            ];
        });

        if (isset($result['status_error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['status_error']);
        }

        $penarikan = $result['penarikan']->load(['nasabah:id,nama,nik,nomor_rw', 'diprosesOleh:id,name']);

        $statusMsg = $targetStatus === 'selesai'
            ? 'Penarikan dana berhasil disetujui, saldo nasabah telah dipotong.'
            : 'Penarikan dana telah ditolak.';

        return response()->json([
            'success' => true,
            'message' => $statusMsg,
            'data' => [
                'id' => $penarikan->id,
                'kode_penarikan' => $penarikan->kode_penarikan,
                'status' => $penarikan->status,
                'nasabah' => [
                    'id' => $penarikan->nasabah->id,
                    'nama' => $penarikan->nasabah->nama,
                    'nik' => $penarikan->nasabah->nik,
                    'saldo_aktif_terbaru' => $result['saldo_sesudah'],
                ],
                'jumlah_penarikan' => (float) $penarikan->jumlah_penarikan,
                'metode_penarikan' => $penarikan->metode_penarikan,
                'diproses_oleh' => $penarikan->diprosesOleh?->name,
                'catatan' => $penarikan->catatan,
                'tanggal_tarik' => $penarikan->tanggal_tarik->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }

    /**
     * Daftar seluruh transaksi penarikan dana (untuk Kader / Admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = TransaksiTarikDana::with(['nasabah:id,nama,nik,no_hp,nomor_rw', 'diprosesOleh:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('nasabah_id')) {
            $query->where('nasabah_id', $request->query('nasabah_id'));
        }

        if ($request->filled('metode_penarikan')) {
            $query->where('metode_penarikan', $request->query('metode_penarikan'));
        }

        $penarikanList = $query->orderBy('tanggal_tarik', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $penarikanList,
        ], 200);
    }

    /**
     * Detail satu transaksi penarikan dana.
     */
    public function show(int $id): JsonResponse
    {
        $penarikan = TransaksiTarikDana::with([
            'nasabah:id,nama,nik,no_hp,nomor_rw,nomor_rt,alamat,saldo_aktif',
            'diprosesOleh:id,name',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $penarikan,
        ], 200);
    }
}
