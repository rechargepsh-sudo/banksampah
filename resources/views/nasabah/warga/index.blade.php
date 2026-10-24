<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    👥 Data Warga Nasabah Bank Sampah
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftar warga lingkungan yang telah bergabung menjadi nasabah aktif bank sampah.</p>
            </div>
            <div class="text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg font-medium">
                Total: <strong>{{ $totalWarga }} Warga</strong> Terdaftar
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Info Notice Privasi --}}
            <div class="p-4 bg-blue-50 border border-blue-200 text-blue-900 rounded-xl text-xs flex items-start gap-2 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-bold">Privasi Terjaga:</span> Informasi yang ditampilkan hanya sebatas nama warga, wilayah domisili RT/RW, dan tanggal bergabung sebagai sesama nasabah lingkungan. Data NIK, kontak pribadi, dan saldo tabungan bersifat rahasia.
                </div>
            </div>

            {{-- Pencarian & Filter --}}
            <div class="bg-white shadow-sm rounded-xl p-4 border border-gray-100">
                <form method="GET" action="{{ route('nasabah.warga.index') }}" class="flex flex-wrap items-center gap-3">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama warga nasabah..."
                           class="flex-1 min-w-56 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs" />

                    <input type="text"
                           name="nomor_rw"
                           value="{{ request('nomor_rw') }}"
                           placeholder="RW"
                           class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs text-center font-mono" />

                    <input type="text"
                           name="nomor_rt"
                           value="{{ request('nomor_rt') }}"
                           placeholder="RT"
                           class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs text-center font-mono" />

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 transition">
                        Cari Warga
                    </button>

                    @if (request()->hasAny(['search', 'nomor_rw', 'nomor_rt']))
                        <a href="{{ route('nasabah.warga.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold hover:bg-gray-200 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Grid Kartu Direktori Warga Nasabah --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($wargaList as $warga)
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-emerald-300 transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($warga->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-sm leading-snug">{{ $warga->nama }}</h3>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            RT {{ $warga->nomor_rt ?? '-' }} / RW {{ $warga->nomor_rw }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if ($warga->alamat)
                                <p class="text-xs text-gray-600 mt-3 line-clamp-2">
                                    📍 {{ $warga->alamat }}
                                </p>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500">
                            <span>Bergabung:</span>
                            <span class="font-semibold text-emerald-700">
                                📅 {{ $warga->tanggal_bergabung_display }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl p-12 text-center text-gray-400 border border-gray-100">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-sm font-medium">Tidak ada data warga nasabah yang sesuai kriteria pencarian.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($wargaList->hasPages())
                <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    {{ $wargaList->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

