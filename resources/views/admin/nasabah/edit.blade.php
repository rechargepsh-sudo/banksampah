<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.nasabah.show', $nasabah) }}" class="text-gray-500 hover:text-gray-700">
                ← Detail Nasabah
            </a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Nasabah: {{ $nasabah->nama }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.nasabah.update', $nasabah) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <h3 class="text-md font-semibold text-gray-700 border-b pb-2">Data Diri Nasabah</h3>

                        {{-- NIK --}}
                        <div>
                            <x-input-label for="nik" :value="__('Nomor Induk Kependudukan (NIK)')" />
                            <x-text-input id="nik" name="nik" type="text" class="mt-1 block w-full"
                                          :value="old('nik', $nasabah->nik)" maxlength="16" required autofocus />
                            <p class="mt-1 text-xs text-gray-500">Tepat 16 digit angka sesuai KTP/KK.</p>
                            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                        </div>

                        {{-- Nama Lengkap --}}
                        <div>
                            <x-input-label for="nama" :value="__('Nama Lengkap')" />
                            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full"
                                          :value="old('nama', $nasabah->nama)" required />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>

                        {{-- No HP --}}
                        <div>
                            <x-input-label for="no_hp" :value="__('Nomor Handphone / WhatsApp')" />
                            <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full"
                                          :value="old('no_hp', $nasabah->no_hp)" required />
                            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                        </div>

                        {{-- Wilayah RW & RT --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nomor_rw" :value="__('Nomor RW')" />
                                <x-text-input id="nomor_rw" name="nomor_rw" type="text" class="mt-1 block w-full"
                                              :value="old('nomor_rw', $nasabah->nomor_rw)" required />
                                <x-input-error :messages="$errors->get('nomor_rw')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="nomor_rt" :value="__('Nomor RT (Opsional)')" />
                                <x-text-input id="nomor_rt" name="nomor_rt" type="text" class="mt-1 block w-full"
                                              :value="old('nomor_rt', $nasabah->nomor_rt)" />
                                <x-input-error :messages="$errors->get('nomor_rt')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Alamat Lengkap --}}
                        <div>
                            <x-input-label for="alamat" :value="__('Alamat Lengkap (Opsional)')" />
                            <textarea id="alamat" name="alamat" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('alamat', $nasabah->alamat) }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>

                        {{-- Kader Pembina --}}
                        <div>
                            <x-input-label for="kader_id" :value="__('Kader Pembina (Opsional)')" />
                            <select id="kader_id" name="kader_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="">-- Didaftarkan oleh Admin / Tanpa Kader --</option>
                                @foreach ($kaders as $kader)
                                    <option value="{{ $kader->id }}" @selected(old('kader_id', $nasabah->created_by) == $kader->id)>
                                        {{ $kader->name }} ({{ '@' . $kader->username }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kader_id')" class="mt-2" />
                        </div>

                        @if ($nasabah->user)
                            <h3 class="text-md font-semibold text-gray-700 border-b pb-2 pt-4">Akun Login Nasabah</h3>

                            {{-- Username --}}
                            <div>
                                <x-input-label for="username" :value="__('Username')" />
                                <x-text-input id="username" name="username" type="text" class="mt-1 block w-full"
                                              :value="old('username', $nasabah->user->username)" required />
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>

                            {{-- Email --}}
                            <div>
                                <x-input-label for="email" :value="__('Email (Opsional)')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                              :value="old('email', $nasabah->user->email)" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- Password Baru --}}
                            <div>
                                <x-input-label for="password" :value="__('Password Baru (Opsional)')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password nasabah.</p>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            {{-- Status Aktif --}}
                            <div class="flex items-center gap-3">
                                <input id="is_active" name="is_active" type="checkbox" value="1"
                                       class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"
                                       @checked(old('is_active', $nasabah->user->is_active ? '1' : '0') === '1') />
                                <x-input-label for="is_active" :value="__('Akun Aktif')" class="mb-0" />
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.nasabah.show', $nasabah) }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <x-primary-button class="bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 focus:ring-emerald-500">
                                Simpan Perubahan
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

