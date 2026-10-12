<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use Illuminate\Http\JsonResponse;

class KategoriSampahController extends Controller
{
    /**
     * Menampilkan daftar seluruh master kategori sampah yang aktif.
     */
    public function index(): JsonResponse
    {
        $kategori = KategoriSampah::where('is_active', true)
            ->orderBy('nama_kategori', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kategori->map(fn ($item) => [
                'id' => $item->id,
                'nama_kategori' => $item->nama_kategori,
                'harga_per_kg' => (float) $item->harga_per_kg,
                'satuan' => $item->satuan,
                'harga_format' => 'Rp '.number_format((float) $item->harga_per_kg, 0, ',', '.').' / '.$item->satuan,
            ]),
        ], 200);
    }
}
