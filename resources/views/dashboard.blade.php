<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                    <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-rose-500">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penarikan Pending</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $penarikanPending }}</p>
                        <p class="mt-1 text-xs text-gray-400">Menunggu persetujuan</p>
                    </div>
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
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Kelola Master Data</p>
                            <p class="mt-1 text-sm text-gray-600">Manajemen akun pengguna dan data nasabah</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700 transition">
                                Kelola User
                            </a>
                            <a href="{{ route('admin.nasabah.index') }}"
                               class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-md hover:bg-emerald-700 transition">
                                Kelola Nasabah
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
                    <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-emerald-500">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nasabah Dibina</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $totalNasabahDibina }}</p>
                        <p class="mt-1 text-xs text-gray-400">nasabah terdaftar</p>
                    </div>

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

                    <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-rose-500">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Penarikan Pending</p>
                        <p class="mt-1 text-3xl font-bold text-gray-800">{{ $penarikanPending }}</p>
                        <p class="mt-1 text-xs text-gray-400">nasabah binaan</p>
                    </div>
                </div>

                {{-- Setoran Terbaru --}}
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700">5 Setoran Terbaru (Dicatat Anda)</h3>
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
                        <div class="bg-white shadow-sm sm:rounded-lg p-5 border-l-4 border-emerald-500">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Saldo Aktif</p>
                            <p class="mt-1 text-3xl font-bold text-gray-800">Rp {{ number_format($saldoAktif, 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-gray-400">saldo dapat ditarik</p>
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
                            <p class="text-xs text-gray-400">RT {{ $nasabah->nomor_rt }} / RW {{ $nasabah->nomor_rw }}</p>
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
    </div>
</x-app-layout>
