<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transaksi Setor Sampah') }}
            </h2>
            <a href="{{ route('setor-sampah.create') }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Catat Setoran Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search & Filter --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" action="{{ route('setor-sampah.index') }}" class="flex flex-wrap gap-3 items-center">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari kode transaksi, nama, atau NIK nasabah..."
                        class="flex-1 min-w-48 rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    />
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Dari:</span>
                        <input
                            type="date"
                            name="tanggal_dari"
                            value="{{ request('tanggal_dari') }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Sampai:</span>
                        <input
                            type="date"
                            name="tanggal_sampai"
                            value="{{ request('tanggal_sampai') }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                        />
                    </div>
                    <x-primary-button>Filter</x-primary-button>
                    @if (request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai']))
                        <a href="{{ route('setor-sampah.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode & Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nasabah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas / Kader</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Berat</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai (Rp)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($transaksiList as $transaksi)
                                <tr class="hover:bg-gray-50 transition-colors duration-100">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-mono font-semibold text-gray-900">{{ $transaksi->kode_transaksi }}</div>
                                        <div class="text-xs text-gray-500">{{ $transaksi->tanggal_setor->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $transaksi->nasabah?->nama ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">NIK: {{ $transaksi->nasabah?->nik ?? '-' }} (RW {{ $transaksi->nasabah?->nomor_rw ?? '-' }})</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $transaksi->kader?->name ?? 'Admin' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-mono text-sm text-gray-800">
                                        {{ number_format($transaksi->total_berat, 2) }} kg
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-mono text-sm font-bold text-emerald-600">
                                        Rp {{ number_format($transaksi->total_nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('setor-sampah.show', $transaksi) }}"
                                           class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded text-xs font-semibold transition">
                                            Lihat Struk
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                                        Belum ada riwayat transaksi setoran sampah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($transaksiList->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $transaksiList->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

