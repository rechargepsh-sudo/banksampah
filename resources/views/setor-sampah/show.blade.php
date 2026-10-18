<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between no-print">
            <div class="flex items-center gap-3">
                <a href="{{ route('setor-sampah.index') }}" class="text-gray-500 hover:text-gray-700">
                    ← Daftar Setoran
                </a>
                <span class="text-gray-400">/</span>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Struk Setoran: {{ $transaksi->kode_transaksi }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Struk
                </button>
                <a href="{{ route('setor-sampah.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 transition">
                    + Setoran Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md no-print text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tampilan Struk / Nota Fisik --}}
            <div id="print-area" class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-8">
                {{-- Header Nota --}}
                <div class="text-center pb-6 border-b-2 border-dashed border-gray-300">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">BANK SAMPAH DIGITAL</h1>
                    <p class="text-xs text-gray-500 mt-1">Sistem Pengelolaan Sampah Daur Ulang Pemukiman RW/RT</p>
                    <div class="inline-block mt-3 px-3 py-1 bg-emerald-50 text-emerald-800 text-xs font-mono font-semibold rounded border border-emerald-200">
                        NOTA BUKTI SETORAN SAMPAH
                    </div>
                </div>

                {{-- Detail Meta & Nasabah --}}
                <div class="grid grid-cols-2 gap-4 py-6 text-sm border-b border-gray-200">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Nomor Transaksi</p>
                        <p class="font-mono font-bold text-gray-800">{{ $transaksi->kode_transaksi }}</p>

                        <p class="text-xs text-gray-400 uppercase font-semibold mt-3">Waktu Setor</p>
                        <p class="text-gray-800">{{ $transaksi->tanggal_setor->translatedFormat('d F Y - H:i') }} WIB</p>

                        <p class="text-xs text-gray-400 uppercase font-semibold mt-3">Kader / Petugas</p>
                        <p class="text-gray-800">{{ $transaksi->kader?->name ?? 'Admin' }}</p>
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-gray-400 uppercase font-semibold">Nama Nasabah</p>
                        <p class="font-bold text-gray-900 text-base">{{ $transaksi->nasabah?->nama ?? '-' }}</p>

                        <p class="text-xs text-gray-400 uppercase font-semibold mt-3">NIK Nasabah</p>
                        <p class="font-mono text-gray-800">{{ $transaksi->nasabah?->nik ?? '-' }}</p>

                        <p class="text-xs text-gray-400 uppercase font-semibold mt-3">Wilayah Domisili</p>
                        <p class="text-gray-800">RW {{ $transaksi->nasabah?->nomor_rw ?? '-' }} / RT {{ $transaksi->nasabah?->nomor_rt ?? '-' }}</p>
                    </div>
                </div>

                {{-- Rincian Item Timbangan --}}
                <div class="py-6">
                    <h3 class="text-xs uppercase font-bold text-gray-500 tracking-wider mb-3">Rincian Timbangan Sampah</h3>
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="text-gray-500 text-xs uppercase">
                                <th class="py-2 text-left">Jenis Sampah</th>
                                <th class="py-2 text-right">Harga Satuan</th>
                                <th class="py-2 text-right">Berat Timbang</th>
                                <th class="py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($transaksi->details as $item)
                                <tr>
                                    <td class="py-3 text-gray-900 font-medium">
                                        {{ $item->kategoriSampah?->nama_kategori ?? '-' }}
                                    </td>
                                    <td class="py-3 text-right font-mono text-xs text-gray-600">
                                        Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}/kg
                                    </td>
                                    <td class="py-3 text-right font-mono text-sm text-gray-800">
                                        {{ number_format($item->berat, 2) }} kg
                                    </td>
                                    <td class="py-3 text-right font-mono font-semibold text-emerald-700">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 font-bold text-gray-900">
                                <td colspan="2" class="py-3 uppercase text-xs">Total Akumulasi:</td>
                                <td class="py-3 text-right font-mono">{{ number_format($transaksi->total_berat, 2) }} kg</td>
                                <td class="py-3 text-right font-mono text-emerald-700 text-base">
                                    Rp {{ number_format($transaksi->total_nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Status Saldo Terkini --}}
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Saldo Aktif Nasabah Saat Ini</span>
                        <p class="text-xs text-gray-400 mt-0.5">Sudah termasuk setoran di atas</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-bold font-mono text-emerald-700">
                            Rp {{ number_format($transaksi->nasabah?->saldo_aktif ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Catatan --}}
                @if ($transaksi->catatan)
                    <div class="mt-4 text-xs text-gray-500">
                        <span class="font-semibold">Catatan:</span> {{ $transaksi->catatan }}
                    </div>
                @endif

                {{-- Footer Struk --}}
                <div class="pt-8 mt-6 border-t border-dashed border-gray-300 text-center text-xs text-gray-400">
                    <p>Terima kasih telah berpartisipasi menjaga kebersihan lingkungan dengan mendaur ulang sampah.</p>
                    <p class="mt-1 font-mono text-gray-300">Dicetak otomatis oleh Sistem Bank Sampah RW</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Print Style --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</x-app-layout>

