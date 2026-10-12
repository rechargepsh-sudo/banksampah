<?php

namespace App\Http\Controllers;

use App\Models\MutasiSaldo;
use App\Models\TransaksiSetor;
use App\Models\TransaksiTarikDana;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NasabahController extends Controller
{
    /**
     * Cek informasi profil lengkap Nasabah yang sedang login.
     * Acceptance Criteria: NIK (16 digit), Nama Lengkap, Nomor HP, Nomor RW.
     */
    public function profil(Request $request): JsonResponse
    {
        $nasabah = $request->user()->nasabah;

        if (! $nasabah) {
            return response()->json([
                'success' => false,
                'message' => 'Profil nasabah tidak ditemukan untuk akun ini.',
            ], 404);
        }

        $nasabah->load(['user:id,username,email,is_active', 'kaderPendaftar:id,name']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $nasabah->id,
                'nik' => $nasabah->nik,
                'nama' => $nasabah->nama,
                'no_hp' => $nasabah->no_hp,
                'nomor_rw' => $nasabah->nomor_rw,
                'nomor_rt' => $nasabah->nomor_rt,
                'alamat' => $nasabah->alamat,
                'saldo_aktif' => (float) $nasabah->saldo_aktif,
                'saldo_format' => 'Rp '.number_format((float) $nasabah->saldo_aktif, 0, ',', '.'),
                'username' => $nasabah->user?->username,
                'email' => $nasabah->user?->email,
                'didaftarkan_oleh' => $nasabah->kaderPendaftar?->name,
                'terdaftar_sejak' => $nasabah->created_at->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }

    /**
     * Cek saldo aktif Nasabah secara mandiri.
     */
    public function saldo(Request $request): JsonResponse
    {
        $nasabah = $request->user()->nasabah;

        if (! $nasabah) {
            return response()->json([
                'success' => false,
                'message' => 'Profil nasabah tidak ditemukan untuk akun ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nasabah_id' => $nasabah->id,
                'nama' => $nasabah->nama,
                'nik' => $nasabah->nik,
                'nomor_rw' => $nasabah->nomor_rw,
                'saldo_aktif' => (float) $nasabah->saldo_aktif,
                'saldo_format' => 'Rp '.number_format((float) $nasabah->saldo_aktif, 0, ',', '.'),
            ],
        ], 200);
    }

    /**
     * Riwayat mutasi saldo Nasabah (uang masuk & keluar).
     */
    public function mutasi(Request $request): JsonResponse
    {
        $nasabah = $request->user()->nasabah;

        if (! $nasabah) {
            return response()->json([
                'success' => false,
                'message' => 'Profil nasabah tidak ditemukan untuk akun ini.',
            ], 404);
        }

        $query = MutasiSaldo::where('nasabah_id', $nasabah->id);

        if ($request->filled('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->query('jenis_mutasi'));
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->query('tanggal_dari'));
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->query('tanggal_sampai'));
        }

        $mutasiList = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'saldo_aktif_saat_ini' => (float) $nasabah->saldo_aktif,
            'data' => $mutasiList,
        ], 200);
    }

    /**
     * Riwayat setoran sampah milik nasabah login.
     */
    public function riwayatSetor(Request $request): JsonResponse
    {
        $nasabah = $request->user()->nasabah;

        if (! $nasabah) {
            return response()->json([
                'success' => false,
                'message' => 'Profil nasabah tidak ditemukan untuk akun ini.',
            ], 404);
        }

        $setoran = TransaksiSetor::with(['kader:id,name', 'details.kategoriSampah'])
            ->where('nasabah_id', $nasabah->id)
            ->orderBy('tanggal_setor', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $setoran,
        ], 200);
    }

    /**
     * Riwayat permohonan penarikan dana milik nasabah login.
     */
    public function riwayatPenarikan(Request $request): JsonResponse
    {
        $nasabah = $request->user()->nasabah;

        if (! $nasabah) {
            return response()->json([
                'success' => false,
                'message' => 'Profil nasabah tidak ditemukan untuk akun ini.',
            ], 404);
        }

        $penarikan = TransaksiTarikDana::with('diprosesOleh:id,name')
            ->where('nasabah_id', $nasabah->id)
            ->orderBy('tanggal_tarik', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $penarikan,
        ], 200);
    }
}
