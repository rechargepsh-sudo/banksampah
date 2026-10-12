<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaksi\StoreTransaksiSetorRequest;
use App\Models\DetailTransaksiSetor;
use App\Models\KategoriSampah;
use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransaksiSetorController extends Controller
{
    /**
     * Input transaksi setoran sampah oleh Kader.
     * Otomatis mengupdate saldo_aktif nasabah dan mencatat mutasi_saldo menggunakan DB transaction.
     */
    public function store(StoreTransaksiSetorRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $kader = $request->user();

        $result = DB::transaction(function () use ($validated, $kader) {
            // Lock record nasabah untuk mencegah race condition pada saldo
            $nasabah = Nasabah::lockForUpdate()->findOrFail($validated['nasabah_id']);

            // Generate kode transaksi unik
            $dateStr = now()->format('Ymd');
            $countToday = TransaksiSetor::whereDate('created_at', today())->count() + 1;
            $kodeTransaksi = "SETOR-{$dateStr}-".str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
            while (TransaksiSetor::where('kode_transaksi', $kodeTransaksi)->exists()) {
                $countToday++;
                $kodeTransaksi = "SETOR-{$dateStr}-".str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
            }

            $totalBerat = 0.0;
            $totalNominal = 0.0;
            $detailsData = [];

            // Hitung subtotal tiap kategori sampah berdasarkan harga master saat ini
            foreach ($validated['items'] as $item) {
                $kategori = KategoriSampah::findOrFail($item['kategori_sampah_id']);

                $berat = (float) $item['berat'];
                $hargaSatuan = (float) $kategori->harga_per_kg;
                $subtotal = round($berat * $hargaSatuan, 2);

                $totalBerat += $berat;
                $totalNominal += $subtotal;

                $detailsData[] = [
                    'kategori_sampah_id' => $kategori->id,
                    'kategori_nama' => $kategori->nama_kategori,
                    'berat' => $berat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];
            }

            $tanggalSetor = ! empty($validated['tanggal_setor'])
                ? Carbon::parse($validated['tanggal_setor'])
                : now();

            // 1. Simpan Transaksi Setor
            $transaksiSetor = TransaksiSetor::create([
                'kode_transaksi' => $kodeTransaksi,
                'nasabah_id' => $nasabah->id,
                'kader_id' => $kader->id,
                'total_berat' => $totalBerat,
                'total_nominal' => $totalNominal,
                'tanggal_setor' => $tanggalSetor,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // 2. Simpan Detail Transaksi
            foreach ($detailsData as $detail) {
                DetailTransaksiSetor::create([
                    'transaksi_setor_id' => $transaksiSetor->id,
                    'kategori_sampah_id' => $detail['kategori_sampah_id'],
                    'berat' => $detail['berat'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $detail['subtotal'],
                ]);
            }

            // 3. Catat Riwayat Mutasi Saldo
            $saldoSebelum = (float) $nasabah->saldo_aktif;
            $saldoSesudah = round($saldoSebelum + $totalNominal, 2);

            $mutasi = MutasiSaldo::create([
                'nasabah_id' => $nasabah->id,
                'jenis_mutasi' => 'masuk',
                'nominal' => $totalNominal,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'referensi_id' => $transaksiSetor->id,
                'tipe_referensi' => 'transaksi_setor',
                'deskripsi' => "Setoran sampah #{$kodeTransaksi} ({$totalBerat} kg)",
            ]);

            // 4. Update Saldo Aktif Nasabah
            $nasabah->update([
                'saldo_aktif' => $saldoSesudah,
            ]);

            return [
                'transaksi' => $transaksiSetor->load('details.kategoriSampah'),
                'mutasi' => $mutasi,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'nasabah' => $nasabah,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi setoran sampah berhasil dicatat dan saldo nasabah telah diperbarui.',
            'data' => [
                'id' => $result['transaksi']->id,
                'kode_transaksi' => $result['transaksi']->kode_transaksi,
                'nasabah' => [
                    'id' => $result['nasabah']->id,
                    'nama' => $result['nasabah']->nama,
                    'nik' => $result['nasabah']->nik,
                    'nomor_rw' => $result['nasabah']->nomor_rw,
                ],
                'kader' => [
                    'id' => $kader->id,
                    'name' => $kader->name,
                ],
                'tanggal_setor' => $result['transaksi']->tanggal_setor->format('Y-m-d H:i:s'),
                'total_berat' => (float) $result['transaksi']->total_berat,
                'total_nominal' => (float) $result['transaksi']->total_nominal,
                'catatan' => $result['transaksi']->catatan,
                'items' => $result['transaksi']->details->map(fn ($detail) => [
                    'kategori' => $detail->kategoriSampah?->nama_kategori,
                    'berat' => (float) $detail->berat,
                    'harga_satuan' => (float) $detail->harga_satuan,
                    'subtotal' => (float) $detail->subtotal,
                ]),
                'saldo' => [
                    'sebelum' => $result['saldo_sebelum'],
                    'bertambah' => (float) $result['transaksi']->total_nominal,
                    'aktif_sekarang' => $result['saldo_sesudah'],
                ],
            ],
        ], 201);
    }

    /**
     * Daftar seluruh transaksi setor (untuk Kader / Admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = TransaksiSetor::with(['nasabah:id,nama,nik,nomor_rw', 'kader:id,name', 'details.kategoriSampah']);

        if ($request->filled('nasabah_id')) {
            $query->where('nasabah_id', $request->query('nasabah_id'));
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_setor', '>=', $request->query('tanggal_dari'));
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_setor', '<=', $request->query('tanggal_sampai'));
        }

        $transaksiList = $query->orderBy('tanggal_setor', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transaksiList,
        ], 200);
    }

    /**
     * Detail satu transaksi setor.
     */
    public function show(int $id): JsonResponse
    {
        $transaksi = TransaksiSetor::with([
            'nasabah:id,nama,nik,no_hp,nomor_rw,nomor_rt,alamat,saldo_aktif',
            'kader:id,name',
            'details.kategoriSampah',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $transaksi,
        ], 200);
    }
}
