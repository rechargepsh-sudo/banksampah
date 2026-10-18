<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">
                ← Kelola User
            </a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail User: {{ $user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Informasi Akun --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Informasi Akun</h3>
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition ease-in-out duration-150">
                            Edit
                        </a>
                    </div>

                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Username</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ '@' . $user->username }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->email ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Role</dt>
                            <dd class="mt-1">
                                @php $roleColors = ['admin' => 'bg-purple-100 text-purple-800', 'kader' => 'bg-blue-100 text-blue-800', 'nasabah' => 'bg-green-100 text-green-800']; @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                            <dd class="mt-1">
                                @if ($user->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Non-Aktif</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Terdaftar</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Terakhir Diperbarui</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $user->transaksi_setor_dicatat_count }}</div>
                    <div class="text-xs text-gray-500 mt-1">Transaksi Setor (dicatat)</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $user->penarikan_diproses_count }}</div>
                    <div class="text-xs text-gray-500 mt-1">Penarikan (diproses)</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $user->nasabahDidaftarkan->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">Nasabah Didaftarkan</div>
                </div>
            </div>

            {{-- Data Nasabah (jika ada) --}}
            @if ($user->nasabah)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Nasabah</h3>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">NIK</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $user->nasabah->nik }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Nama</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->nasabah->nama }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">No. HP</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->nasabah->no_hp }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">RW / RT</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->nasabah->nomor_rw }} / {{ $user->nasabah->nomor_rt ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Alamat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->nasabah->alamat ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Saldo Aktif</dt>
                                <dd class="mt-1 text-sm font-semibold text-green-700">Rp {{ number_format($user->nasabah->saldo_aktif, 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif

            {{-- Hapus User --}}
            @if ($user->id !== auth()->id())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-red-200">
                    <div class="p-6">
                        <h3 class="text-base font-semibold text-red-700 mb-2">Zona Berbahaya</h3>
                        <p class="text-sm text-gray-600 mb-4">Menghapus user akan menghilangkan data akun secara permanen.</p>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Yakin ingin menghapus user {{ addslashes($user->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>Hapus User Ini</x-danger-button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

