<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Permohonan Penarikan Dana') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ modalSetuju: false, modalTolak: false, currentId: null, currentKode: '', currentNama: '', currentJumlah: '', currentMetode: '', currentCatatan: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Status Tabs / Filter Chips --}}
            <div class="flex flex-wrap gap-2 mb-6">
                <a href="{{ route('penarikan-dana.index') }}"
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition {{ !request('status') ? 'bg-gray-800 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                    Semua ({{ $stats['total'] }})
                </a>
                <a href="{{ route('penarikan-dana.index', ['status' => 'pending']) }}"
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-amber-600 text-white' : 'bg-white text-amber-700 hover:bg-amber-50 border border-amber-200' }}">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    Menunggu Validasi ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('penarikan-dana.index', ['status' => 'selesai']) }}"
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition {{ request('status') === 'selesai' ? 'bg-emerald-600 text-white' : 'bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-200' }}">
                    Disetujui / Selesai ({{ $stats['selesai'] }})
                </a>
                <a href="{{ route('penarikan-dana.index', ['status' => 'ditolak']) }}"
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition {{ request('status') === 'ditolak' ? 'bg-rose-600 text-white' : 'bg-white text-rose-700 hover:bg-rose-50 border border-rose-200' }}">
                    Ditolak ({{ $stats['ditolak'] }})
                </a>
            </div>

            {{-- Search & Date Filter --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" action="{{ route('penarikan-dana.index') }}" class="flex flex-wrap gap-3 items-center">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}" />
                    @endif
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari kode penarikan, nama nasabah, NIK..."
                        class="flex-1 min-w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    />
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Dari:</span>
                        <input
                            type="date"
                            name="tanggal_dari"
                            value="{{ request('tanggal_dari') }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Sampai:</span>
                        <input
                            type="date"
                            name="tanggal_sampai"
                            value="{{ request('tanggal_sampai') }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        />
                    </div>
                    <x-primary-button>Filter</x-primary-button>
                    @if (request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai']))
                        <a href="{{ route('penarikan-dana.index', ['status' => request('status')]) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Kode & Tanggal</th>
                                <th class="px-6 py-3 text-left">Nasabah</th>
                                <th class="px-6 py-3 text-right">Jumlah Tarik</th>
                                <th class="px-6 py-3 text-left">Metode</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Petugas</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($penarikanList as $penarikan)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-mono font-semibold text-sm text-gray-900">{{ $penarikan->kode_penarikan }}</div>
                                        <div class="text-xs text-gray-500">{{ $penarikan->tanggal_tarik->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $penarikan->nasabah?->nama ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">NIK: {{ $penarikan->nasabah?->nik ?? '-' }} (RW {{ $penarikan->nasabah?->nomor_rw ?? '-' }})</div>
                                        <div class="text-xs text-emerald-600 font-medium">Saldo: Rp {{ number_format($penarikan->nasabah?->saldo_aktif ?? 0, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-mono font-bold text-sm text-rose-600">
                                        Rp {{ number_format($penarikan->jumlah_penarikan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold capitalize {{ $penarikan->metode_penarikan === 'transfer' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ $penarikan->metode_penarikan }}
                                        </span>
                                        @if ($penarikan->catatan)
                                            <p class="text-xs text-gray-400 truncate max-w-xs mt-0.5" title="{{ $penarikan->catatan }}">{{ $penarikan->catatan }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($penarikan->status === 'selesai')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">
                                                Disetujui
                                            </span>
                                        @elseif ($penarikan->status === 'ditolak')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">
                                                Menunggu Validasi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                        {{ $penarikan->diprosesOleh?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-1">
                                        @if ($penarikan->status === 'pending')
                                            {{-- Tombol Setujui --}}
                                            <button type="button"
                                                    @click="modalSetuju = true; currentId = {{ $penarikan->id }}; currentKode = '{{ $penarikan->kode_penarikan }}'; currentNama = '{{ addslashes($penarikan->nasabah?->nama ?? '') }}'; currentJumlah = 'Rp {{ number_format($penarikan->jumlah_penarikan, 0, ',', '.') }}'; currentMetode = '{{ ucfirst($penarikan->metode_penarikan) }}';"
                                                    class="inline-flex items-center px-2.5 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-500 transition">
                                                Setujui
                                            </button>

                                            {{-- Tombol Tolak --}}
                                            <button type="button"
                                                    @click="modalTolak = true; currentId = {{ $penarikan->id }}; currentKode = '{{ $penarikan->kode_penarikan }}'; currentNama = '{{ addslashes($penarikan->nasabah?->nama ?? '') }}'; currentJumlah = 'Rp {{ number_format($penarikan->jumlah_penarikan, 0, ',', '.') }}';"
                                                    class="inline-flex items-center px-2.5 py-1 bg-red-600 text-white rounded hover:bg-red-500 transition">
                                                Tolak
                                            </button>
                                        @else
                                            <a href="{{ route('penarikan-dana.show', $penarikan) }}"
                                               class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded transition">
                                                Struk
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                                        Tidak ada permohonan penarikan dana pada kriteria ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($penarikanList->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $penarikanList->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal Setujui Penarikan --}}
        <div x-show="modalSetuju" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="modalSetuju = false">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-bold text-gray-900">Setujui Penarikan Dana</h3>
                    <button type="button" @click="modalSetuju = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="text-sm text-gray-600 space-y-2">
                    <p>Konfirmasi persetujuan penarikan dana:</p>
                    <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-lg text-xs space-y-1">
                        <div><strong>Nasabah:</strong> <span x-text="currentNama"></span></div>
                        <div><strong>Kode Transaksi:</strong> <span class="font-mono" x-text="currentKode"></span></div>
                        <div><strong>Jumlah Penarikan:</strong> <span class="font-bold text-emerald-800" x-text="currentJumlah"></span></div>
                        <div><strong>Metode:</strong> <span x-text="currentMetode"></span></div>
                    </div>
                    <p class="text-xs text-amber-700 bg-amber-50 p-2 rounded">
                        * Menyetujui akan langsung memotong saldo aktif nasabah dan mencatat riwayat mutasi keluar.
                    </p>
                </div>

                <form :action="'/penarikan-dana/' + currentId + '/validasi'" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="status" value="selesai" />
                    <div>
                        <x-input-label for="catatan_setuju" :value="__('Catatan Tambahan (Opsional)')" />
                        <x-text-input id="catatan_setuju" name="catatan" type="text" class="mt-1 block w-full text-xs"
                                      placeholder="Contoh: Dana diserahkan tunai di Pos RW" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="modalSetuju = false"
                                class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-md hover:bg-emerald-500 transition">
                            Ya, Setujui & Potong Saldo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Tolak Penarikan --}}
        <div x-show="modalTolak" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="modalTolak = false">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-bold text-red-700">Tolak Permohonan Penarikan</h3>
                    <button type="button" @click="modalTolak = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="text-sm text-gray-600 space-y-2">
                    <p>Apakah Anda yakin ingin menolak permohonan penarikan dana ini?</p>
                    <div class="bg-red-50 border border-red-200 p-3 rounded-lg text-xs space-y-1">
                        <div><strong>Nasabah:</strong> <span x-text="currentNama"></span></div>
                        <div><strong>Jumlah:</strong> <span class="font-bold text-red-800" x-text="currentJumlah"></span></div>
                    </div>
                    <p class="text-xs text-gray-500">Saldo nasabah tidak akan berubah jika permohonan ditolak.</p>
                </div>

                <form :action="'/penarikan-dana/' + currentId + '/validasi'" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="status" value="ditolak" />
                    <div>
                        <x-input-label for="catatan_tolak" :value="__('Alasan Penolakan')" />
                        <x-text-input id="catatan_tolak" name="catatan" type="text" class="mt-1 block w-full text-xs"
                                      placeholder="Contoh: Kas tunai pos RW sedang kosong / rekening salah" required />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="modalTolak = false"
                                class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-md hover:bg-red-500 transition">
                            Tolak Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

