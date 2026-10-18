<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKategoriSampahRequest;
use App\Http\Requests\Admin\UpdateKategoriSampahRequest;
use App\Models\KategoriSampah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriSampahController extends Controller
{
    /**
     * Menampilkan daftar master kategori sampah.
     */
    public function index(Request $request): View
    {
        $query = KategoriSampah::query();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->query('status') === 'aktif');
        }

        $kategoriList = $query->orderByRaw('kode IS NULL, kode ASC')->orderBy('nama_kategori')->paginate(15)->withQueryString();

        $totalAktif = KategoriSampah::where('is_active', true)->count();
        $totalNonAktif = KategoriSampah::where('is_active', false)->count();

        return view('admin.kategori-sampah.index', compact('kategoriList', 'totalAktif', 'totalNonAktif'));
    }

    /**
     * Menampilkan form tambah kategori sampah baru.
     */
    public function create(): View
    {
        return view('admin.kategori-sampah.create');
    }

    /**
     * Menyimpan kategori sampah baru.
     */
    public function store(StoreKategoriSampahRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        KategoriSampah::create($validated);

        return redirect()->route('admin.kategori-sampah.index')
            ->with('success', "Kategori sampah \"{$validated['nama_kategori']}\" berhasil ditambahkan.");
    }

    /**
     * Menampilkan detail/halaman show (redirect ke edit karena data sederhana).
     */
    public function show(KategoriSampah $kategoriSampah): RedirectResponse
    {
        return redirect()->route('admin.kategori-sampah.edit', $kategoriSampah);
    }

    /**
     * Menampilkan form edit kategori sampah.
     */
    public function edit(KategoriSampah $kategoriSampah): View
    {
        $jumlahDipakai = $kategoriSampah->detailTransaksiSetor()->count();

        return view('admin.kategori-sampah.edit', compact('kategoriSampah', 'jumlahDipakai'));
    }

    /**
     * Memperbarui data kategori sampah.
     */
    public function update(UpdateKategoriSampahRequest $request, KategoriSampah $kategoriSampah): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', false);

        $kategoriSampah->update($validated);

        return redirect()->route('admin.kategori-sampah.index')
            ->with('success', "Kategori sampah \"{$kategoriSampah->nama_kategori}\" berhasil diperbarui.");
    }

    /**
     * Menghapus kategori sampah (hanya jika tidak ada transaksi terkait).
     */
    public function destroy(KategoriSampah $kategoriSampah): RedirectResponse
    {
        $jumlahDipakai = $kategoriSampah->detailTransaksiSetor()->count();

        if ($jumlahDipakai > 0) {
            return redirect()->route('admin.kategori-sampah.index')
                ->with('error', "Kategori \"{$kategoriSampah->nama_kategori}\" tidak dapat dihapus karena sudah digunakan dalam {$jumlahDipakai} transaksi. Nonaktifkan saja jika tidak ingin digunakan lagi.");
        }

        $namaKategori = $kategoriSampah->nama_kategori;
        $kategoriSampah->delete();

        return redirect()->route('admin.kategori-sampah.index')
            ->with('success', "Kategori sampah \"{$namaKategori}\" berhasil dihapus.");
    }

    /**
     * Toggle status aktif / non-aktif secara cepat.
     */
    public function toggleStatus(KategoriSampah $kategoriSampah): RedirectResponse
    {
        $kategoriSampah->update(['is_active' => ! $kategoriSampah->is_active]);

        $status = $kategoriSampah->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Kategori \"{$kategoriSampah->nama_kategori}\" berhasil {$status}.");
    }
}
