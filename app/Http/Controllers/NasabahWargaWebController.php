<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NasabahWargaWebController extends Controller
{
    /**
     * Menampilkan daftar nasabah warga untuk role Nasabah.
     * Informasi dibatasi hanya: nama, alamat RT dan RW, dan tanggal bergabung.
     */
    public function index(Request $request): View
    {
        $query = Nasabah::query()
            ->select(['id', 'nama', 'nomor_rw', 'nomor_rt', 'alamat', 'tanggal_bergabung', 'created_at']);

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('nama', 'like', "%{$search}%");
        }

        if ($request->filled('nomor_rw')) {
            $query->where('nomor_rw', $request->query('nomor_rw'));
        }

        if ($request->filled('nomor_rt')) {
            $query->where('nomor_rt', $request->query('nomor_rt'));
        }

        $wargaList = $query->orderBy('nama', 'asc')->paginate(12)->withQueryString();
        $totalWarga = Nasabah::count();

        return view('nasabah.warga.index', compact('wargaList', 'totalWarga'));
    }
}

