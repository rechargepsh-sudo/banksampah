<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">
                ← Kelola User
            </a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah User Baru
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                        @csrf

                        {{-- Nama --}}
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                          :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        {{-- Username --}}
                        <div>
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full"
                                          :value="old('username')" required />
                            <p class="mt-1 text-xs text-gray-500">Hanya huruf, angka, tanda hubung (-), dan garis bawah (_).</p>
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Email (Opsional)')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                          :value="old('email')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Password --}}
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter.</p>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        {{-- Role --}}
                        <div>
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                <option value="kader" @selected(old('role') === 'kader')>Kader</option>
                                <option value="nasabah" @selected(old('role') === 'nasabah')>Nasabah</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        {{-- Status Aktif --}}
                        <div class="flex items-center gap-3">
                            <input id="is_active" name="is_active" type="checkbox" value="1"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                   @checked(old('is_active', '1') === '1') />
                            <x-input-label for="is_active" :value="__('Akun Aktif')" class="mb-0" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <x-primary-button>Simpan User</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

