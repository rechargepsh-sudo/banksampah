<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('kader.nasabah.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Profil Nasabah: <span class="text-emerald-700">{{ $nasabah->nama }}</span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Detail identitas, saldo tabungan, dan riwayat setoran sampah.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kader.nasabah.edit', $nasabah) }}"
                   class="inline-flex items-center px-3 py-2 bg-amber-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 shadow-sm transition">
                    ✏️ Edit Profil
                </a>
                <form method="POST" action="{{ route('kader.nasabah.destroy', $nasabah) }}"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data nasabah {{ addslashes($nasabah->nama) }}? Semua akun dan data terkait akan ikut terhapus.')"
                      class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-rose-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-700 shadow-sm transition">
                        🗑️ Hapus
                    </button>
                </form>
                <a href="{{ route('setor-sampah.create', ['nasabah_id' => $nasabah->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 shadow-sm transition">
                    + Setor Sampah
                </a>
            </div>
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

            {{-- Kartu Info Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Info Saldo --}}
                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-6 text-white shadow-md flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Saldo Tabungan Aktif</span>
                        <div class="text-3xl font-extrabold font-mono mt-2">
                            Rp {{ number_format((float) $nasabah->saldo_aktif, 0, ',', '.') }}
                        </div>
                        <p class="text-xs text-emerald-100 mt-1">Dapat ditarik tunai saat jam operasional.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-emerald-500/50 flex items-center justify-between">
                        <span class="text-xs text-emerald-100">Status Akun:</span>
                        <span class="px-2 py-0.5 bg-emerald-500/50 text-white rounded text-xs font-semibold">Aktif</span>
                    </div>
                </div>

                {{-- Info Kependudukan --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 md:col-span-2 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Informasi Kependudukan & Kontak</h3>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-gray-500 block">NIK:</span>
                            <span class="font-bold text-gray-900 font-mono text-sm">{{ $nasabah->nik }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Nama Lengkap:</span>
                            <span class="font-bold text-gray-900 text-sm">{{ $nasabah->nama }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Nomor HP / WA:</span>
                            <span class="font-semibold text-gray-800 font-mono">{{ $nasabah->no_hp }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Wilayah Domisili:</span>
                            <span class="font-semibold text-gray-800">RT {{ $nasabah->nomor_rt ?? '-' }} / RW {{ $nasabah->nomor_rw }}</span>
                        <div>
                            <span class="text-gray-500 block">Tanggal Bergabung:</span>
                            <span class="font-semibold text-emerald-800">📅 {{ $nasabah->tanggal_bergabung_display }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Kader Pembina:</span>
                            <span class="font-semibold text-gray-800">
                                {{ $nasabah->kaderPendaftar?->name ?? 'Admin' }}
                                @if ($nasabah->kaderPendaftar?->nomor_rw)
                                    <span class="px-1.5 py-0.5 bg-teal-100 text-teal-800 rounded text-[10px] font-bold">RW {{ $nasabah->kaderPendaftar->nomor_rw }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500 block">Alamat Lengkap:</span>
                            <span class="text-gray-700">{{ $nasabah->alamat ?? 'Belum diisi' }}</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                        <div class="text-gray-500">
                            Username: <strong class="font-mono text-emerald-700">{{ '@' . ($nasabah->user?->username ?? '-') }}</strong>
                        </div>
                        <div class="text-gray-400">
                            Terdaftar di sistem: {{ $nasabah->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat Setoran & Mutasi --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- 5 Setoran Terakhir --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center justify-between">
                        <span>Setoran Sampah Terakhir</span>
                        <span class="text-xs text-gray-400 font-normal">Maks 5 data</span>
                    </h3>

                    @if ($nasabah->transaksiSetor->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($nasabah->transaksiSetor as $setor)
                                <div class="p-3 bg-gray-50 rounded-xl flex items-center justify-between text-xs">
                                    <div>
                                        <div class="font-bold text-gray-800 font-mono">{{ $setor->kode_transaksi }}</div>
                                        <div class="text-gray-400 mt-0.5">{{ $setor->tanggal_setor?->format('d/m/Y H:i') }} &bull; {{ number_format((float) $setor->total_berat, 2) }} kg</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-emerald-700 font-mono">+ Rp {{ number_format((float) $setor->total_nominal, 0, ',', '.') }}</div>
                                        <a href="{{ route('setor-sampah.show', $setor) }}" class="text-[11px] text-blue-600 hover:underline">Rincian &rarr;</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 py-6 text-center">Belum ada riwayat setoran sampah.</p>
                    @endif
                </div>

                {{-- 10 Mutasi Saldo Terakhir --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center justify-between">
                        <span>Riwayat Mutasi Saldo</span>
                        <span class="text-xs text-gray-400 font-normal">Maks 10 data</span>
                    </h3>

                    @if ($nasabah->mutasiSaldo->isNotEmpty())
                        <div class="space-y-2">
                            @foreach ($nasabah->mutasiSaldo as $mutasi)
                                <div class="p-2.5 rounded-lg border border-gray-100 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-semibold text-gray-800 capitalize">{{ $mutasi->deskripsi ?? 'Transaksi' }}</span>
                                        <div class="text-[11px] text-gray-400 font-mono">{{ $mutasi->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <div class="text-right font-mono font-bold {{ $mutasi->jenis_mutasi === 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $mutasi->jenis_mutasi === 'masuk' ? '+' : '-' }} Rp {{ number_format((float) $mutasi->nominal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 py-6 text-center">Belum ada mutasi saldo tercatat.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

