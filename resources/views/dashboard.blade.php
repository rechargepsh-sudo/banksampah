<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8" x-data="{ modalTarik: false, isSubmitting: false }" x-on:keydown.escape.window="modalTarik = false">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-md text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Greeting --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-xl shrink-0">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-lg font-semibold text-gray-800">Selamat datang, {{ $user->name }}!</p>
                    <p class="text-sm text-gray-500 capitalize">Role: {{ $user->role }} &mdash; {{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ADMIN DASHBOARD --}}
            {{-- ============================================================ --}}
            @if ($user->role === 'admin')

                {{-- Stat Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {{-- Total User --}}
                    <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-indigo-500">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total User</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $totalUser }}</p>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ $totalAdmin }} Admin &bull; {{ $totalKader }} Kader &bull; {{ $totalNasabah }} Nasabah
                        </p>
                    </div>

                    {{-- Nasabah Terdaftar --}}
                    <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-emerald-500">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nasabah Terdaftar</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $totalNasabahTerdaftar }}</p>
                        <p class="mt-1 text-xs text-gray-400">Total saldo: Rp {{ number_format($totalSaldoAktif, 0, ',', '.') }}</p>
                    </div>

                    {{-- Setoran Bulan Ini --}}
                    <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-amber-500">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Setoran Bulan Ini</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $setoranBulanIni }}</p>
                        <p class="mt-1 text-xs text-gray-400">Rp {{ number_format($nominalBulanIni, 0, ',', '.') }}</p>
                    </div>

                    {{-- Penarikan Pending --}}
                    <a href="{{ route('penarikan-dana.index', ['status' => 'pending']) }}"
                       class="block bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-rose-500 hover:bg-rose-50/40 transition">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penarikan Pending</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $penarikanPending }}</p>
                        <p class="mt-1 text-xs text-rose-600 font-medium">Buka antrean validasi →</p>
                    </a>
                </div>

                {{-- Ringkasan Keseluruhan --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white shadow-sm sm:rounded-lg p-5">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Setoran Sepanjang Waktu</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalSetoran }} transaksi</p>
                        <p class="text-sm text-gray-500">Rp {{ number_format($totalNominalSetoran, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Kelola Master Data & Kas</p>
                            <p class="mt-1 text-sm text-gray-600">Manajemen akun, data nasabah, dan penarikan</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('setor-sampah.create') }}"
                               class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-md hover:bg-emerald-700 transition">
                                + Setor Sampah
                            </a>
                            <a href="{{ route('penarikan-dana.index') }}"
                               class="inline-flex items-center px-3 py-2 bg-rose-600 text-white text-xs font-medium rounded-md hover:bg-rose-700 transition">
                                Penarikan Dana
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700 transition">
                                Kelola User
                            </a>
                            <a href="{{ route('admin.kategori-sampah.index') }}"
                               class="inline-flex items-center px-3 py-2 bg-amber-600 text-white text-xs font-medium rounded-md hover:bg-amber-700 transition">
                                ♻️ Kategori Sampah
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Setoran Terbaru --}}
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700">5 Setoran Terbaru</h3>
                    </div>
                    @if ($setoranTerbaru->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nasabah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kader</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nominal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($setoranTerbaru as $setor)
                                    <tr>
                                        <td class="px-6 py-3 font-mono text-xs text-gray-600">{{ $setor->kode_transaksi }}</td>
                                        <td class="px-6 py-3 text-gray-800">{{ $setor->nasabah?->nama ?? '-' }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $setor->kader?->name ?? '-' }}</td>
                                        <td class="px-6 py-3 text-right text-gray-800">Rp {{ number_format($setor->total_nominal, 0, ',', '.') }}</td>
                                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $setor->tanggal_setor?->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="px-6 py-4 text-sm text-gray-400">Belum ada data setoran.</p>
                    @endif
                </div>

            {{-- ============================================================ --}}
            {{-- KADER DASHBOARD --}}
            {{-- ============================================================ --}}
            @elseif ($user->role === 'kader')

                {{-- Stat Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('kader.nasabah.index') }}"
                       class="block bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-emerald-500 hover:bg-emerald-50/40 transition">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nasabah Dibina</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $totalNasabahDibina }}</p>
                        <p class="mt-1 text-xs text-emerald-600 font-medium">Kelola nasabah →</p>
                    </a>

                    <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-indigo-500">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Setoran</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $totalSetoran }}</p>
                        <p class="mt-1 text-xs text-gray-400">Rp {{ number_format($totalNominalSetoran, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-amber-500">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Setoran Bulan Ini</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $setoranBulanIni }}</p>
                        <p class="mt-1 text-xs text-gray-400">Rp {{ number_format($nominalBulanIni, 0, ',', '.') }}</p>
                    </div>

                    <a href="{{ route('penarikan-dana.index', ['status' => 'pending']) }}"
                       class="block bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-rose-500 hover:bg-rose-50/40 transition">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penarikan Pending</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $penarikanPending }}</p>
                        <p class="mt-1 text-xs text-rose-600 font-medium">Validasi antrean →</p>
                    </a>
                </div>

                {{-- Action Bar Kader --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi Cepat Kader</p>
                        <p class="mt-1 text-sm text-gray-600">Catat penimbangan sampah warga atau daftarkan nasabah baru</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('kader.nasabah.create') }}"
                           class="inline-flex items-center px-3 py-2 bg-teal-600 text-white text-xs font-medium rounded-md hover:bg-teal-700 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            + Tambah Nasabah
                        </a>
                        <a href="{{ route('kader.nasabah.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-md hover:bg-gray-200 transition">
                            Daftar Nasabah
                        </a>
                        <a href="{{ route('setor-sampah.create') }}"
                           class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-md hover:bg-emerald-700 transition">
                            + Timbang Sampah
                        </a>
                        <a href="{{ route('penarikan-dana.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-rose-600 text-white text-xs font-medium rounded-md hover:bg-rose-700 transition">
                            Validasi Penarikan
                        </a>
                        <a href="{{ route('katalog-sampah.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-amber-500 text-white text-xs font-medium rounded-md hover:bg-amber-600 transition">
                            ♻️ Kategori Sampah
                        </a>
                    </div>
                </div>

                {{-- Setoran Terbaru --}}
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">5 Setoran Terbaru (Dicatat Anda)</h3>
                            <p class="text-xs text-gray-500">Daftar transaksi penimbangan sampah terakhir</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('setor-sampah.create') }}"
                               class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-md hover:bg-emerald-500 shadow-sm transition">
                                + Timbang / Catat Setoran
                            </a>
                            <a href="{{ route('setor-sampah.index') }}"
                               class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-200 transition">
                                Semua Riwayat
                            </a>
                        </div>
                    </div>
                    @if ($setoranTerbaru->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nasabah</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nominal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($setoranTerbaru as $setor)
                                    <tr>
                                        <td class="px-6 py-3 font-mono text-xs text-gray-600">{{ $setor->kode_transaksi }}</td>
                                        <td class="px-6 py-3 text-gray-800">{{ $setor->nasabah?->nama ?? '-' }}</td>
                                        <td class="px-6 py-3 text-right text-gray-800">Rp {{ number_format($setor->total_nominal, 0, ',', '.') }}</td>
                                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $setor->tanggal_setor?->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="px-6 py-4 text-sm text-gray-400">Belum ada data setoran yang Anda catat.</p>
                    @endif
                </div>

            {{-- ============================================================ --}}
            {{-- NASABAH DASHBOARD --}}
            {{-- ============================================================ --}}
            @else

                @if (! $nasabah)
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg p-5">
                        <p class="font-semibold">Profil nasabah belum tersedia.</p>
                        <p class="text-sm mt-1">Hubungi kader Anda untuk mendaftarkan profil nasabah.</p>
                    </div>
                @else
                    {{-- Stat Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-emerald-500 flex flex-col justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Saldo Aktif</p>
                                <p class="mt-1 text-3xl font-bold text-gray-800 font-mono">Rp {{ number_format($saldoAktif, 0, ',', '.') }}</p>
                                <p class="mt-1 text-xs text-gray-400">saldo siap dicairkan</p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <button type="button" @click="modalTarik = true"
                                        @if ($saldoAktif <= 0) disabled @endif
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-md hover:bg-emerald-500 disabled:opacity-40 disabled:cursor-not-allowed transition">
                                    Ajukan Tarik Dana
                                </button>
                            </div>
                        </div>

                        <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-indigo-500">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Setoran</p>
                            <p class="mt-1 text-3xl font-bold text-gray-800">{{ $totalSetoran }}</p>
                            <p class="mt-1 text-xs text-gray-400">Rp {{ number_format($totalNominalSetoran, 0, ',', '.') }}</p>
                        </div>

                        <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-sky-500">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Info Rekening</p>
                            <p class="mt-1 text-sm font-semibold text-gray-800">{{ $nasabah->nama }}</p>
                            <p class="text-xs text-gray-400">NIK: {{ $nasabah->nik }}</p>
                            <p class="text-xs text-gray-500">RT {{ $nasabah->nomor_rt ?? '-' }} / RW {{ $nasabah->nomor_rw }}</p>
                            <p class="mt-1 text-xs text-emerald-700 font-medium">📅 Bergabung: {{ $nasabah->tanggal_bergabung_display }}</p>
                        </div>
                    </div>

                    {{-- Action Bar Nasabah --}}
                    <div class="bg-white shadow-sm sm:rounded-lg p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Layanan Mandiri Warga</p>
                            <p class="mt-1 text-sm text-gray-600">Cek daftar sesama warga nasabah atau patokan harga terkini kategori sampah.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('nasabah.warga.index') }}"
                               class="inline-flex items-center px-3 py-2 bg-teal-600 text-white text-xs font-medium rounded-md hover:bg-teal-700 transition shadow-sm">
                                👥 Data Nasabah
                            </a>
                            <a href="{{ route('katalog-sampah.index') }}"
                               class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-md hover:bg-emerald-700 transition shadow-sm">
                                ♻️ Kategori Sampah
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Riwayat Setoran --}}
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-700">5 Setoran Terakhir</h3>
                            </div>
                            @if ($setoranTerbaru->isNotEmpty())
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nominal</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($setoranTerbaru as $setor)
                                            <tr>
                                                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $setor->kode_transaksi }}</td>
                                                <td class="px-4 py-3 text-right text-gray-800">Rp {{ number_format($setor->total_nominal, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $setor->tanggal_setor?->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="px-6 py-4 text-sm text-gray-400">Belum ada riwayat setoran.</p>
                            @endif
                        </div>

                        {{-- Riwayat Penarikan --}}
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-700">5 Penarikan Terakhir</h3>
                            </div>
                            @if ($penarikanTerbaru->isNotEmpty())
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($penarikanTerbaru as $tarik)
                                            <tr>
                                                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $tarik->kode_penarikan }}</td>
                                                <td class="px-4 py-3 text-right text-gray-800">Rp {{ number_format($tarik->jumlah_penarikan, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3">
                                                    @php
                                                        $statusClass = match($tarik->status) {
                                                            'selesai'  => 'bg-green-100 text-green-700',
                                                            'ditolak'  => 'bg-red-100 text-red-700',
                                                            default    => 'bg-amber-100 text-amber-700',
                                                        };
                                                        $statusLabel = match($tarik->status) {
                                                            'selesai'  => 'Selesai',
                                                            'ditolak'  => 'Ditolak',
                                                            default    => 'Pending',
                                                        };
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusClass }}">
                                                        {{ $statusLabel }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="px-6 py-4 text-sm text-gray-400">Belum ada riwayat penarikan.</p>
                            @endif
                        </div>

                    </div>
                @endif
            @endif

        </div>

        {{-- Modal Pengajuan Tarik Dana --}}
        @if ($user->role === 'nasabah' && !empty($nasabah))
            <div x-show="modalTarik"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
                 @click.self="modalTarik = false">
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 space-y-4 relative"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     @click.away="modalTarik = false">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h3 class="text-base font-bold text-gray-900">Ajukan Penarikan Dana</h3>
                        <button type="button"
                                @click="modalTarik = false"
                                class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-100 transition text-lg font-bold leading-none"
                                title="Tutup (Esc)">
                            &times;
                        </button>
                    </div>

                    <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-lg text-xs flex justify-between items-center">
                        <div>
                            <span class="text-gray-600">Saldo Aktif Saat Ini:</span>
                            <div class="text-lg font-bold text-emerald-800 font-mono">Rp {{ number_format($saldoAktif, 0, ',', '.') }}</div>
                        </div>
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded font-semibold text-xs">Siap Ditarik</span>
                    </div>

                    <form action="{{ route('penarikan-dana.ajukan') }}"
                          method="POST"
                          class="space-y-4"
                          @submit="modalTarik = false; isSubmitting = true">
                        @csrf
                        <div>
                            <x-input-label for="jumlah_penarikan" :value="__('Nominal Penarikan (Rp)')" />
                            <x-text-input id="jumlah_penarikan" name="jumlah_penarikan" type="number" step="500" min="1000" max="{{ $saldoAktif }}"
                                          class="mt-1 block w-full text-sm font-mono" placeholder="Minimal Rp 1.000" required />
                            <p class="text-xs text-gray-400 mt-1">Maksimal penarikan: Rp {{ number_format($saldoAktif, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <x-input-label for="metode_penarikan" :value="__('Metode Pengambilan Dana')" />
                            <select id="metode_penarikan" name="metode_penarikan" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="tunai">Ambil Tunai di Pos Bank Sampah RW</option>
                                <option value="transfer">Transfer Bank / E-Wallet</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="catatan" :value="__('Keterangan / No. Rekening (Opsional)')" />
                            <textarea id="catatan" name="catatan" rows="2"
                                      placeholder="Nama bank, nomor rekening/e-wallet, atau waktu pengambilan"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t">
                            <button type="button"
                                    @click="modalTarik = false"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-200 transition">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="isSubmitting"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                                <span x-show="!isSubmitting">Kirim Permohonan</span>
                                <span x-show="isSubmitting" class="flex items-center gap-1">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Mengirim...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
