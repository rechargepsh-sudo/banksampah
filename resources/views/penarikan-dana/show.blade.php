<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between no-print">
            <div class="flex items-center gap-3">
                <a href="{{ route('penarikan-dana.index') }}" class="text-gray-500 hover:text-gray-700">
                    ← Daftar Penarikan
                </a>
                <span class="text-gray-400">/</span>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Kwitansi Penarikan: {{ $penarikan->kode_penarikan }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Kwitansi
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div id="print-area" class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-8">
                {{-- Header --}}
                <div class="text-center pb-6 border-b-2 border-dashed border-gray-300">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">BANK SAMPAH DIGITAL</h1>
                    <p class="text-xs text-gray-500 mt-1">Sistem Pengelolaan Sampah Daur Ulang Pemukiman RW/RT</p>
                    <div class="inline-block mt-3 px-3 py-1 bg-indigo-50 text-indigo-800 text-xs font-mono font-semibold rounded border border-indigo-200">
                        KWITANSI PENARIKAN TABUNGAN
                    </div>
                </div>

                {{-- Status Badge --}}
                <div class="text-center py-4 border-b border-gray-100">
                    @if ($penarikan->status === 'selesai')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 uppercase tracking-wider">
                            ✓ Penarikan Selesai / Disetujui
                        </span>
                    @elseif ($penarikan->status === 'ditolak')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 uppercase tracking-wider">
                            ✗ Penarikan Ditolak
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">
                            ⏳ Menunggu Validasi Kader
                        </span>
                    @endif
                </div>

                {{-- Rincian Penarikan --}}
                <div class="py-6 space-y-4 text-sm border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-xs uppercase font-semibold">Nomor Kwitansi:</span>
                        <span class="font-mono font-bold text-gray-900">{{ $penarikan->kode_penarikan }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-xs uppercase font-semibold">Waktu Penarikan:</span>
                        <span class="text-gray-800">{{ $penarikan->tanggal_tarik->translatedFormat('d F Y - H:i') }} WIB</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-xs uppercase font-semibold">Nama Nasabah:</span>
                        <span class="font-bold text-gray-900">{{ $penarikan->nasabah?->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-xs uppercase font-semibold">NIK Nasabah:</span>
                        <span class="font-mono text-gray-800">{{ $penarikan->nasabah?->nik ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-xs uppercase font-semibold">Wilayah Domisili:</span>
                        <span class="text-gray-800">RW {{ $penarikan->nasabah?->nomor_rw ?? '-' }} / RT {{ $penarikan->nasabah?->nomor_rt ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-xs uppercase font-semibold">Metode Penarikan:</span>
                        <span class="font-semibold uppercase text-xs text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">{{ $penarikan->metode_penarikan }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-xs uppercase font-semibold">Petugas Validasi:</span>
                        <span class="text-gray-800">{{ $penarikan->diprosesOleh?->name ?? 'Belum diproses' }}</span>
                    </div>
                    @if ($penarikan->catatan)
                        <div class="flex justify-between items-start pt-2 border-t border-gray-100">
                            <span class="text-gray-500 text-xs uppercase font-semibold">Catatan / No. Rek:</span>
                            <span class="text-gray-800 text-right max-w-xs">{{ $penarikan->catatan }}</span>
                        </div>
                    @endif
                </div>

                {{-- Nominal Penarikan Box --}}
                <div class="bg-rose-50 border border-rose-200 rounded-lg p-5 my-6 text-center">
                    <span class="text-xs uppercase font-bold text-rose-800 tracking-wider">Jumlah Dana Ditarik</span>
                    <div class="text-3xl font-bold font-mono text-rose-700 mt-1">
                        Rp {{ number_format($penarikan->jumlah_penarikan, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Saldo Sisa --}}
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 flex items-center justify-between text-sm">
                    <span class="text-gray-500 text-xs uppercase font-semibold">Sisa Saldo Aktif Nasabah Saat Ini:</span>
                    <span class="font-mono font-bold text-emerald-700 text-base">
                        Rp {{ number_format($penarikan->nasabah?->saldo_aktif ?? 0, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Tanda Tangan Kwitansi --}}
                <div class="grid grid-cols-2 gap-8 pt-10 text-center text-xs text-gray-700">
                    <div>
                        <p>Penerima Dana (Nasabah)</p>
                        <div class="h-16"></div>
                        <p class="font-semibold underline">({{ $penarikan->nasabah?->nama ?? '..........................' }})</p>
                    </div>
                    <div>
                        <p>Petugas Validasi (Kader)</p>
                        <div class="h-16"></div>
                        <p class="font-semibold underline">({{ $penarikan->diprosesOleh?->name ?? '..........................' }})</p>
                    </div>
                </div>

                {{-- Footer Struk --}}
                <div class="pt-8 mt-6 border-t border-dashed border-gray-300 text-center text-xs text-gray-400">
                    <p class="font-mono text-gray-300">Dicetak otomatis oleh Sistem Bank Sampah RW</p>
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

