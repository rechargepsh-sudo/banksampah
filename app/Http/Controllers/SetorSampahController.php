<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaksi\StoreTransaksiSetorRequest;
use App\Models\DetailTransaksiSetor;
use App\Models\KategoriSampah;
use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SetorSampahController extends Controller
{
    /**
     * Menampilkan riwayat transaksi setor sampah untuk Kader & Admin.
     */
    public function index(Request $request): View
    {
        $query = TransaksiSetor::query()
            ->with(['nasabah', 'kader', 'details.kategoriSampah']);

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                    ->orWhereHas('nasabah', function ($nq) use ($search) {
                        $nq->where('nama', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_setor', '>=', $request->query('tanggal_dari'));
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_setor', '<=', $request->query('tanggal_sampai'));
        }

        $transaksiList = $query->orderBy('tanggal_setor', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('setor-sampah.index', compact('transaksiList'));
    }

    /**
     * Menampilkan formulir penimbangan dan pencatatan setoran sampah baru.
     */
    public function create(Request $request): View
    {
        $nasabahList = Nasabah::with('user:id,username')
            ->orderBy('nama', 'asc')
            ->get();

        $kategoriList = KategoriSampah::where('is_active', true)
            ->orderBy('nama_kategori', 'asc')
            ->get();

        $selectedNasabahId = $request->query('nasabah_id');

        return view('setor-sampah.create', compact('nasabahList', 'kategoriList', 'selectedNasabahId'));
    }

    /**
     * Menyimpan transaksi setoran sampah dan mengupdate saldo nasabah secara atomik.
     */
    public function store(StoreTransaksiSetorRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $kader = $request->user();

        $transaksiSetor = DB::transaction(function () use ($validated, $kader) {
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

            foreach ($validated['items'] as $item) {
                $kategori = KategoriSampah::findOrFail($item['kategori_sampah_id']);

                $berat = (float) $item['berat'];
                $hargaSatuan = (float) $kategori->harga_per_kg;
                $subtotal = round($berat * $hargaSatuan, 2);

                $totalBerat += $berat;
                $totalNominal += $subtotal;

                $detailsData[] = [
                    'kategori_sampah_id' => $kategori->id,
                    'berat' => $berat,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ];
            }

            $tanggalSetor = ! empty($validated['tanggal_setor'])
                ? Carbon::parse($validated['tanggal_setor'])
                : now();

            $transaksi = TransaksiSetor::create([
                'kode_transaksi' => $kodeTransaksi,
                'nasabah_id' => $nasabah->id,
                'kader_id' => $kader->id,
                'total_berat' => $totalBerat,
                'total_nominal' => $totalNominal,
                'tanggal_setor' => $tanggalSetor,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            foreach ($detailsData as $detail) {
                DetailTransaksiSetor::create([
                    'transaksi_setor_id' => $transaksi->id,
                    'kategori_sampah_id' => $detail['kategori_sampah_id'],
                    'berat' => $detail['berat'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $detail['subtotal'],
                ]);
            }

            $saldoSebelum = (float) $nasabah->saldo_aktif;
            $saldoSesudah = round($saldoSebelum + $totalNominal, 2);

            MutasiSaldo::create([
                'nasabah_id' => $nasabah->id,
                'jenis_mutasi' => 'masuk',
                'nominal' => $totalNominal,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'referensi_id' => $transaksi->id,
                'tipe_referensi' => 'transaksi_setor',
                'deskripsi' => "Setoran sampah #{$kodeTransaksi} ({$totalBerat} kg)",
            ]);

            $nasabah->update([
                'saldo_aktif' => $saldoSesudah,
            ]);

            return $transaksi;
        });

        return redirect()->route('setor-sampah.show', $transaksiSetor)
            ->with('success', 'Transaksi setoran sampah berhasil dicatat. Saldo nasabah telah ditambahkan!');
    }

    /**
     * Menampilkan rincian nota / struk setoran sampah.
     */
    public function show(TransaksiSetor $setor_sampah): View
    {
        $setor_sampah->load(['nasabah', 'kader', 'details.kategoriSampah']);

        return view('setor-sampah.show', [
            'transaksi' => $setor_sampah,
        ]);
    }
}
