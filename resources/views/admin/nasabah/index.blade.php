<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Nasabah') }}
            </h2>
            <a href="{{ route('admin.nasabah.create') }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Tambah Nasabah
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

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Search & Filter --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" action="{{ route('admin.nasabah.index') }}" class="flex flex-wrap gap-3">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, NIK, atau nomor HP..."
                        class="flex-1 min-w-48 rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    />
                    <input
                        type="text"
                        name="nomor_rw"
                        value="{{ request('nomor_rw') }}"
                        placeholder="Nomor RW"
                        class="w-28 rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    />
                    <input
                        type="text"
                        name="nomor_rt"
                        value="{{ request('nomor_rt') }}"
                        placeholder="Nomor RT"
                        class="w-28 rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    />
                    <x-primary-button>Filter</x-primary-button>
                    @if (request()->hasAny(['search', 'nomor_rw', 'nomor_rt']))
                        <a href="{{ route('admin.nasabah.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition ease-in-out duration-150">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK & Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akun Login</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah & Kontak</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kader Pembina</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Aktif</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($nasabahList as $nasabah)
                                <tr class="hover:bg-gray-50 transition-colors duration-100">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $nasabah->nama }}</div>
                                        <div class="text-xs font-mono text-gray-500">{{ $nasabah->nik }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($nasabah->user)
                                            <div class="text-xs font-mono text-gray-800">{{ '@' . $nasabah->user->username }}</div>
                                            <div>
                                                @if ($nasabah->user->is_active)
                                                    <span class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-700">Aktif</span>
                                                @else
                                                    <span class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded bg-red-100 text-red-700">Non-Aktif</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">Tidak ada akun</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-800">RW {{ $nasabah->nomor_rw }} / RT {{ $nasabah->nomor_rt ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $nasabah->no_hp }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $nasabah->kaderPendaftar?->name ?? 'Admin / Langsung' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-bold text-emerald-600">
                                            Rp {{ number_format($nasabah->saldo_aktif, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.nasabah.show', $nasabah) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        <a href="{{ route('admin.nasabah.edit', $nasabah) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                        <form method="POST" action="{{ route('admin.nasabah.destroy', $nasabah) }}" class="inline"
                                              onsubmit="return confirm('Yakin ingin menghapus nasabah {{ addslashes($nasabah->nama) }}? Akun dan riwayat terkait juga akan dihapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                                        Tidak ada data nasabah yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($nasabahList->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $nasabahList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

