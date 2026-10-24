<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    ♻️ Daftar Harga & Kategori Sampah
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Informasi jenis sampah terpilah yang diterima beserta patokan harga terkini.</p>
            </div>
            <div class="text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg font-medium">
                Tersedia <strong>{{ count($kategoriList) }} Jenis</strong> Sampah Daur Ulang
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Pencarian --}}
            <div class="bg-white shadow-sm rounded-xl p-4 border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('katalog-sampah.index') }}" class="w-full sm:w-96 flex items-center gap-2">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari jenis sampah (misal: Kardus, Botol, Besi)..."
                           class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs" />
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 transition">
                        Cari
                    </button>
                    @if (request()->filled('search'))
                        <a href="{{ route('katalog-sampah.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold hover:bg-gray-200 transition">
                            Reset
                        </a>
                    @endif
                </form>
                <div class="text-xs text-gray-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Harga berlaku per satuan berat tertera saat penimbangan</span>
                </div>
            </div>

            {{-- Tabel Kategori Sampah (Hanya Jenis, Harga, Satuan Berat) --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5 text-center w-16">No</th>
                                <th class="px-6 py-3.5 text-left">Jenis Sampah</th>
                                <th class="px-6 py-3.5 text-right">Harga Sampah</th>
                                <th class="px-6 py-3.5 text-center">Satuan Berat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($kategoriList as $index => $kategori)
                                <tr class="hover:bg-emerald-50/40 transition">
                                    {{-- No --}}
                                    <td class="px-6 py-4 text-center text-xs text-gray-400 font-mono">
                                        {{ $index + 1 }}
                                    </td>

                                    {{-- Jenis Sampah --}}
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            {{ $kategori->nama_kategori }}
                                        </div>
                                    </td>

                                    {{-- Harga Sampah --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="text-base font-bold font-mono text-emerald-700">
                                            Rp {{ number_format((float) $kategori->harga_per_kg, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">per {{ $kategori->satuan }}</div>
                                    </td>

                                    {{-- Satuan Berat --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 uppercase">
                                            {{ $kategori->satuan }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <p class="text-sm font-medium">Jenis sampah tidak ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tips Sederhana --}}
            <div class="p-5 bg-emerald-50/70 border border-emerald-100 rounded-xl text-xs text-emerald-900 flex items-start gap-3 shadow-sm">
                <span class="text-lg">💡</span>
                <div>
                    <h4 class="font-bold mb-1">Tips Pemilahan Sampah Sebelum Disetor:</h4>
                    <p class="text-emerald-800 leading-relaxed">
                        Pastikan sampah dalam keadaan kering dan terbebas dari sisa makanan atau kotoran basah. Pilah berdasarkan kategori (misalnya pisahkan botol plastik bening dengan kardus/kertas) agar proses penimbangan di pos bank sampah berjalan cepat.
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

