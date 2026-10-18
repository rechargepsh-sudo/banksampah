<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    ♻️ Master Kategori Sampah
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Kelola jenis sampah daur ulang dan harga per kg yang menjadi acuan penimbangan.</p>
            </div>
            <a href="{{ route('admin.kategori-sampah.create') }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md flex items-start gap-2">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md flex items-start gap-2">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $kategoriList->total() }}</div>
                        <div class="text-xs text-gray-500">Total Kategori</div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $totalAktif }}</div>
                        <div class="text-xs text-gray-500">Aktif (Tersedia di Form Setor)</div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $totalNonAktif }}</div>
                        <div class="text-xs text-gray-500">Non-Aktif</div>
                    </div>
                </div>
            </div>

            {{-- Search & Filter --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" action="{{ route('admin.kategori-sampah.index') }}" class="flex flex-wrap gap-3">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari kode (misal: 1A, 2D, 3F) atau nama kategori..."
                           class="flex-1 min-w-48 rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" />
                    <select name="status"
                            class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif Saja</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif Saja</option>
                    </select>
                    <x-primary-button>Filter</x-primary-button>
                    @if (request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.kategori-sampah.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition ease-in-out duration-150">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{}">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode & Nama Kategori</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga / Satuan</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dipakai di Transaksi</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($kategoriList as $kategori)
                                <tr class="hover:bg-gray-50 transition-colors duration-100 {{ $kategori->is_active ? '' : 'opacity-60' }}">
                                    {{-- Nama Kategori --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2.5">
                                            @if ($kategori->kode)
                                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 font-mono tracking-wide border border-emerald-200 flex-shrink-0">
                                                    {{ $kategori->kode }}
                                                </span>
                                            @endif
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $kategori->nama_kategori }}</div>
                                                <div class="text-xs text-gray-400">ID: #{{ $kategori->id }} &bull; Dibuat {{ $kategori->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Harga --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-bold text-emerald-700">
                                            Rp {{ number_format((float) $kategori->harga_per_kg, 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-gray-400">per {{ $kategori->satuan }}</div>
                                    </td>

                                    {{-- Satuan --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ strtoupper($kategori->satuan) }}
                                        </span>
                                    </td>

                                    {{-- Pemakaian --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php $jumlahPakai = $kategori->detailTransaksiSetor()->count(); @endphp
                                        @if ($jumlahPakai > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                {{ $jumlahPakai }} transaksi
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">Belum dipakai</span>
                                        @endif
                                    </td>

                                    {{-- Status Toggle --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <form method="POST"
                                              action="{{ route('admin.kategori-sampah.toggle-status', $kategori) }}"
                                              x-data="{}"
                                              @submit.prevent="$el.submit()">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold cursor-pointer transition
                                                           {{ $kategori->is_active
                                                              ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                                              : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                                    title="{{ $kategori->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                                                {{ $kategori->is_active ? '✓ Aktif' : '✗ Non-Aktif' }}
                                            </button>
                                        </form>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.kategori-sampah.edit', $kategori) }}"
                                               class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                                                Edit
                                            </a>
                                            @if ($kategori->detailTransaksiSetor()->count() === 0)
                                                <form method="POST"
                                                      action="{{ route('admin.kategori-sampah.destroy', $kategori) }}"
                                                      x-data="{}"
                                                      @submit.prevent="if(confirm('Hapus kategori \"{{ addslashes($kategori->nama_kategori) }}\"? Tindakan ini tidak dapat dibatalkan.')) $el.submit()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1.5 border border-gray-200 rounded text-xs font-medium text-gray-400 cursor-not-allowed"
                                                      title="Tidak dapat dihapus karena sudah digunakan dalam transaksi">
                                                    Hapus
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-sm font-medium">Tidak ada kategori sampah ditemukan.</p>
                                            <p class="text-xs mt-1">
                                                <a href="{{ route('admin.kategori-sampah.create') }}" class="text-emerald-600 hover:underline">+ Tambah kategori pertama</a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($kategoriList->hasPages())
                    <div class="border-t border-gray-200 px-6 py-3">
                        {{ $kategoriList->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

