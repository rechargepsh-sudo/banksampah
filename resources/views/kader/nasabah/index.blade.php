<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    👥 Data & Pendaftaran Nasabah
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Kelola dan daftarkan warga lingkungan yang menjadi nasabah bank sampah.</p>
            </div>
            <a href="{{ route('kader.nasabah.create') }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 shadow-sm transition">
                + Tambah Nasabah Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-xl text-sm flex items-start gap-2 shadow-sm">
                    <svg class="w-5 h-5 flex-shrink-0 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['total_nasabah'] }}</div>
                        <div class="text-xs text-gray-500">Total Nasabah Terdaftar</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['nasabah_saya'] }}</div>
                        <div class="text-xs text-gray-500">Nasabah yang Saya Daftarkan</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-emerald-700 font-mono">Rp {{ number_format($stats['total_saldo'], 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-500">Total Saldo Nasabah</div>
                    </div>
                </div>
            </div>

            {{-- Search & Filter Bar --}}
            <div class="bg-white shadow-sm rounded-xl p-4 border border-gray-100">
                <form method="GET" action="{{ route('kader.nasabah.index') }}" class="flex flex-wrap items-center gap-3">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama, NIK (16 digit), atau nomor HP..."
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

                    <select name="lingkup"
                            class="rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs">
                        <option value="">Semua Nasabah</option>
                        <option value="saya" {{ request('lingkup') === 'saya' ? 'selected' : '' }}>Binaan Saya Saja</option>
                    </select>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 transition">
                        Filter
                    </button>

                    @if (request()->hasAny(['search', 'nomor_rw', 'nomor_rt', 'lingkup']))
                        <a href="{{ route('kader.nasabah.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold hover:bg-gray-200 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table List Nasabah --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Nasabah</th>
                                <th class="px-6 py-3 text-left">Akun Login</th>
                                <th class="px-6 py-3 text-left">Wilayah & Kader RW</th>
                                <th class="px-6 py-3 text-left">Tgl Bergabung</th>
                                <th class="px-6 py-3 text-right">Saldo Aktif</th>
                                <th class="px-6 py-3 text-center">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($nasabahList as $nasabah)
                                <tr class="hover:bg-gray-50 transition">
                                    {{-- NIK & Nama --}}
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $nasabah->nama }}</div>
                                        <div class="text-xs font-mono text-gray-500">NIK: {{ $nasabah->nik }}</div>
                                    </td>

                                    {{-- Akun Login --}}
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-mono text-emerald-800 font-semibold bg-emerald-50 px-2 py-0.5 rounded inline-block">
                                            {{ '@' . ($nasabah->user?->username ?? '-') }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            Pwd awal: ...{{ substr($nasabah->nik, -6) }}
                                        </div>
                                    </td>

                                    {{-- Wilayah & Kontak & Kader RW --}}
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-800 font-semibold">
                                            RT {{ $nasabah->nomor_rt ?? '-' }} / RW {{ $nasabah->nomor_rw }}
                                        </div>
                                        <div class="text-[11px] text-gray-500 font-mono">{{ $nasabah->no_hp }}</div>
                                        @if ($nasabah->kaderPendaftar)
                                            <div class="text-[11px] text-teal-700 font-medium mt-1 inline-flex items-center gap-1">
                                                <span>Pembina: {{ $nasabah->kaderPendaftar->name }}</span>
                                                @if ($nasabah->kaderPendaftar->nomor_rw)
                                                    <span class="px-1.5 py-0.2 bg-teal-100 text-teal-800 rounded text-[10px] font-bold">RW {{ $nasabah->kaderPendaftar->nomor_rw }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Tanggal Bergabung --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            📅 {{ $nasabah->tanggal_bergabung_display }}
                                        </span>
                                    </td>

                                    {{-- Saldo Aktif --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-bold font-mono text-emerald-700">
                                            Rp {{ number_format((float) $nasabah->saldo_aktif, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">Siap dicairkan</div>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            <a href="{{ route('setor-sampah.create', ['nasabah_id' => $nasabah->id]) }}"
                                               class="inline-flex items-center px-2.5 py-1.5 bg-emerald-600 text-white rounded-md text-xs font-semibold hover:bg-emerald-700 shadow-sm transition"
                                               title="Timbang & Catat Setoran Sampah untuk nasabah ini">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Setor
                                            </a>
                                            <a href="{{ route('kader.nasabah.show', $nasabah) }}"
                                               class="inline-flex items-center px-2 py-1.5 bg-gray-100 text-gray-700 rounded-md text-xs font-medium hover:bg-gray-200 transition"
                                               title="Lihat profil detail">
                                                Detail
                                            </a>
                                            <a href="{{ route('kader.nasabah.edit', $nasabah) }}"
                                               class="inline-flex items-center px-2 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-md text-xs font-medium hover:bg-amber-100 transition"
                                               title="Edit data nasabah dan akun login">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('kader.nasabah.destroy', $nasabah) }}"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data nasabah {{ addslashes($nasabah->nama) }}? Semua akun dan data terkait akan ikut terhapus.')"
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-2 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-md text-xs font-medium hover:bg-rose-100 transition"
                                                        title="Hapus nasabah">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="text-sm font-medium">Belum ada data nasabah yang cocok.</p>
                                        <p class="text-xs mt-1">
                                            <a href="{{ route('kader.nasabah.create') }}" class="text-emerald-600 hover:underline font-semibold">+ Daftarkan Nasabah Baru</a>
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($nasabahList->hasPages())
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/50">
                        {{ $nasabahList->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

