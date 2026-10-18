<?php

namespace App\Http\Controllers;

use App\Http\Requests\Penarikan\AjukanPenarikanRequest;
use App\Http\Requests\Penarikan\ValidasiPenarikanRequest;
use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use App\Models\TransaksiTarikDana;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TarikDanaWebController extends Controller
{
    /**
     * Menampilkan daftar permohonan penarikan dana untuk Kader & Admin.
     */
    public function index(Request $request): View
    {
        $query = TransaksiTarikDana::query()
            ->with(['nasabah', 'diprosesOleh']);

        if ($request->filled('status') && in_array($request->query('status'), ['pending', 'selesai', 'ditolak'])) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('kode_penarikan', 'like', "%{$search}%")
                    ->orWhereHas('nasabah', function ($nq) use ($search) {
                        $nq->where('nama', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_tarik', '>=', $request->query('tanggal_dari'));
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_tarik', '<=', $request->query('tanggal_sampai'));
        }

        $penarikanList = $query->orderBy('status', 'asc')
            ->orderBy('tanggal_tarik', 'desc')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => TransaksiTarikDana::count(),
            'pending' => TransaksiTarikDana::where('status', 'pending')->count(),
            'selesai' => TransaksiTarikDana::where('status', 'selesai')->count(),
            'ditolak' => TransaksiTarikDana::where('status', 'ditolak')->count(),
        ];

        return view('penarikan-dana.index', compact('penarikanList', 'stats'));
    }

    /**
     * Pengajuan permohonan penarikan dana oleh Nasabah dari Web UI.
     */
    public function ajukan(AjukanPenarikanRequest $request): RedirectResponse
    {
        $nasabah = $request->user()->nasabah;

        if (! $nasabah) {
            return back()->with('error', 'Profil nasabah tidak ditemukan untuk akun ini.');
        }

        $jumlahPenarikan = (float) $request->validated('jumlah_penarikan');

        if ($nasabah->saldo_aktif < $jumlahPenarikan) {
            return back()->withInput()->with('error', 'Saldo aktif Anda (Rp '.number_format($nasabah->saldo_aktif, 0, ',', '.').') tidak mencukupi untuk menarik Rp '.number_format($jumlahPenarikan, 0, ',', '.').'.');
        }

        // Generate kode penarikan unik
        $dateStr = now()->format('Ymd');
        $countToday = TransaksiTarikDana::whereDate('created_at', today())->count() + 1;
        $kodePenarikan = "TARIK-{$dateStr}-".str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
        while (TransaksiTarikDana::where('kode_penarikan', $kodePenarikan)->exists()) {
            $countToday++;
            $kodePenarikan = "TARIK-{$dateStr}-".str_pad((string) $countToday, 4, '0', STR_PAD_LEFT);
        }

        TransaksiTarikDana::create([
            'kode_penarikan' => $kodePenarikan,
            'nasabah_id' => $nasabah->id,
            'jumlah_penarikan' => $jumlahPenarikan,
            'metode_penarikan' => $request->validated('metode_penarikan'),
            'status' => 'pending',
            'diproses_oleh' => null,
            'tanggal_tarik' => now(),
            'catatan' => $request->validated('catatan'),
        ]);

        return redirect()->route('dashboard')->with('success', 'Permohonan penarikan dana sebesar Rp '.number_format($jumlahPenarikan, 0, ',', '.').' berhasil diajukan! Menunggu persetujuan Kader.');
    }

    /**
     * Validasi (Setujui / Tolak) permohonan penarikan dana oleh Kader atau Admin.
     */
    public function validasi(ValidasiPenarikanRequest $request, TransaksiTarikDana $penarikan): RedirectResponse
    {
        $kader = $request->user();
        $targetStatus = $request->validated('status');
        $catatanTambahan = $request->validated('catatan');

        $result = DB::transaction(function () use ($penarikan, $targetStatus, $catatanTambahan, $kader) {
            $penarikanLocked = TransaksiTarikDana::lockForUpdate()->findOrFail($penarikan->id);

            if ($penarikanLocked->status !== 'pending') {
                return [
                    'error' => "Permohonan penarikan ini sudah diproses sebelumnya dengan status [{$penarikanLocked->status}].",
                ];
            }

            $nasabah = Nasabah::lockForUpdate()->findOrFail($penarikanLocked->nasabah_id);

            if ($targetStatus === 'selesai') {
                $jumlah = (float) $penarikanLocked->jumlah_penarikan;
                $saldoSebelum = (float) $nasabah->saldo_aktif;

                if ($saldoSebelum < $jumlah) {
                    return [
                        'error' => 'Saldo aktif nasabah (Rp '.number_format($saldoSebelum, 0, ',', '.').') tidak mencukupi untuk menyelesaikan penarikan sebesar Rp '.number_format($jumlah, 0, ',', '.').'.',
                    ];
                }

                $saldoSesudah = round($saldoSebelum - $jumlah, 2);

                MutasiSaldo::create([
                    'nasabah_id' => $nasabah->id,
                    'jenis_mutasi' => 'keluar',
                    'nominal' => $jumlah,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                    'referensi_id' => $penarikanLocked->id,
                    'tipe_referensi' => 'transaksi_tarik_dana',
                    'deskripsi' => "Penarikan dana ({$penarikanLocked->metode_penarikan}) #{$penarikanLocked->kode_penarikan}",
                ]);

                $nasabah->update([
                    'saldo_aktif' => $saldoSesudah,
                ]);

                $penarikanLocked->update([
                    'status' => 'selesai',
                    'diproses_oleh' => $kader->id,
                    'catatan' => $catatanTambahan ?? $penarikanLocked->catatan,
                ]);

                return [
                    'success' => 'Penarikan dana berhasil DISETUJUI. Saldo nasabah telah dipotong sebesar Rp '.number_format($jumlah, 0, ',', '.').'.',
                ];
            }

            // Jika status ditolak
            $penarikanLocked->update([
                'status' => 'ditolak',
                'diproses_oleh' => $kader->id,
                'catatan' => $catatanTambahan ?? $penarikanLocked->catatan,
            ]);

            return [
                'success' => 'Permohonan penarikan dana berhasil DITOLAK. Saldo nasabah tidak berubah.',
            ];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        return back()->with('success', $result['success']);
    }

    /**
     * Menampilkan struk / detail penarikan dana.
     */
    public function show(TransaksiTarikDana $penarikan): View
    {
        $penarikan->load(['nasabah', 'diprosesOleh']);

        return view('penarikan-dana.show', compact('penarikan'));
    }
}
