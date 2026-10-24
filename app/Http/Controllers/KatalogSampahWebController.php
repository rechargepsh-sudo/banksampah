<?php

namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogSampahWebController extends Controller
{
    /**
     * Menampilkan katalog kategori sampah untuk Nasabah & Kader.
     * Informasi hanya terbatas pada: jenis sampah, harga sampah, dan satuan berat.
     */
    public function index(Request $request): View
    {
        $query = KategoriSampah::query()
            ->where('is_active', true)
            ->select(['id', 'nama_kategori', 'harga_per_kg', 'satuan']);

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('nama_kategori', 'like', "%{$search}%");
        }

        $kategoriList = $query->orderBy('nama_kategori', 'asc')->get();

        return view('katalog-sampah.index', compact('kategoriList'));
    }
}

