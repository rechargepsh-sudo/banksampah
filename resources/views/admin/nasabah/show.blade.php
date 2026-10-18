<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.nasabah.index') }}" class="text-gray-500 hover:text-gray-700">
                ← Kelola Nasabah
            </a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Nasabah: {{ $nasabah->nama }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Informasi Utama & Saldo --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Card Saldo --}}
                <div class="bg-emerald-700 text-white shadow-sm sm:rounded-lg p-6 flex flex-col justify-between">
                    <div>
                        <span class="text-xs uppercase tracking-wider text-emerald-200 font-semibold">Saldo Aktif Saat Ini</span>
                        <div class="text-3xl font-bold mt-2">Rp {{ number_format($nasabah->saldo_aktif, 0, ',', '.') }}</div>
                        <p class="text-xs text-emerald-100 mt-1">Dapat ditarik melalui pengajuan penarikan dana</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-emerald-600/60 flex items-center justify-between text-xs text-emerald-100">
                        <span>Kader Pembina:</span>
                        <span class="font-semibold">{{ $nasabah->kaderPendaftar?->name ?? 'Admin' }}</span>
                    </div>
                </div>

                {{-- Card Akun Login --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6 md:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-800">Akun Login Nasabah</h3>
                        <a href="{{ route('admin.nasabah.edit', $nasabah) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition ease-in-out duration-150">
                            Edit Nasabah
                        </a>
                    </div>
                    @if ($nasabah->user)
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Username</dt>
                                <dd class="mt-1 font-mono text-gray-900">{{ '@' . $nasabah->user->username }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Email</dt>
                                <dd class="mt-1 text-gray-900">{{ $nasabah->user->email ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status Akun</dt>
                                <dd class="mt-1">
                                    @if ($nasabah->user->is_active)
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Non-Aktif</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Role</dt>
                                <dd class="mt-1">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Nasabah</span>
                                </dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-500">Nasabah ini belum memiliki akun login terkait.</p>
                    @endif
                </div>

            </div>

            {{-- Profil Data Kependudukan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">Data Profil Kependudukan</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3 text-sm">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">NIK</dt>
                            <dd class="mt-1 font-mono font-medium text-gray-900">{{ $nasabah->nik }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Nama Lengkap</dt>
                            <dd class="mt-1 font-medium text-gray-900">{{ $nasabah->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">No. Handphone</dt>
                            <dd class="mt-1 text-gray-900">{{ $nasabah->no_hp }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Wilayah</dt>
                            <dd class="mt-1 text-gray-900">RW {{ $nasabah->nomor_rw }} / RT {{ $nasabah->nomor_rt ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Terdaftar Sejak</dt>
                            <dd class="mt-1 text-gray-900">{{ $nasabah->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Alamat Lengkap</dt>
                            <dd class="mt-1 text-gray-900">{{ $nasabah->alamat ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- 5 Setoran Terakhir --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">5 Transaksi Setoran Sampah Terakhir</h3>
                </div>
                @if ($setoranTerbaru->isNotEmpty())
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kader Pencatat</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Berat</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Nominal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($setoranTerbaru as $setor)
                                <tr>
                                    <td class="px-6 py-3 font-mono text-xs text-gray-700">{{ $setor->kode_transaksi }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $setor->kader?->name ?? '-' }}</td>
                                    <td class="px-6 py-3 text-right text-gray-800">{{ number_format($setor->total_berat, 2) }} kg</td>
                                    <td class="px-6 py-3 text-right font-medium text-emerald-600">Rp {{ number_format($setor->total_nominal, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $setor->tanggal_setor?->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="px-6 py-4 text-sm text-gray-400">Belum ada riwayat transaksi setoran.</p>
                @endif
            </div>

            {{-- 5 Penarikan Terakhir --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">5 Riwayat Penarikan Dana Terakhir</h3>
                </div>
                @if ($penarikanTerbaru->isNotEmpty())
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diproses Oleh</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($penarikanTerbaru as $tarik)
                                <tr>
                                    <td class="px-6 py-3 font-mono text-xs text-gray-700">{{ $tarik->kode_penarikan }}</td>
                                    <td class="px-6 py-3 capitalize text-gray-600">{{ $tarik->metode_penarikan }}</td>
                                    <td class="px-6 py-3 text-right font-medium text-rose-600">Rp {{ number_format($tarik->jumlah_penarikan, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3">
                                        @php
                                            $statusColors = [
                                                'selesai' => 'bg-green-100 text-green-700',
                                                'ditolak' => 'bg-red-100 text-red-700',
                                                'pending' => 'bg-amber-100 text-amber-700',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$tarik->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($tarik->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-600">{{ $tarik->diprosesOleh?->name ?? '-' }}</td>
                                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $tarik->tanggal_tarik?->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="px-6 py-4 text-sm text-gray-400">Belum ada riwayat penarikan dana.</p>
                @endif
            </div>

            {{-- Zona Berbahaya --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-red-200">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-red-700 mb-2">Zona Berbahaya</h3>
                    <p class="text-sm text-gray-600 mb-4">Menghapus nasabah akan menghapus data profil kependudukan, akun login terkait, dan riwayat mutasi.</p>
                    <form method="POST" action="{{ route('admin.nasabah.destroy', $nasabah) }}"
                          onsubmit="return confirm('Yakin ingin menghapus nasabah {{ addslashes($nasabah->nama) }}? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>Hapus Nasabah Ini</x-danger-button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

