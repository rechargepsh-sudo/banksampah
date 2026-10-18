<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.nasabah.index') }}" class="text-gray-500 hover:text-gray-700">
                ← Kelola Nasabah
            </a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Nasabah Baru
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.nasabah.store') }}" class="space-y-6">
                        @csrf

                        <h3 class="text-md font-semibold text-gray-700 border-b pb-2">Data Diri Nasabah</h3>

                        {{-- NIK --}}
                        <div>
                            <x-input-label for="nik" :value="__('Nomor Induk Kependudukan (NIK)')" />
                            <x-text-input id="nik" name="nik" type="text" class="mt-1 block w-full"
                                          :value="old('nik')" maxlength="16" required autofocus placeholder="Contoh: 3201012304990001" />
                            <p class="mt-1 text-xs text-gray-500">Tepat 16 digit angka sesuai KTP/KK.</p>
                            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                        </div>

                        {{-- Nama Lengkap --}}
                        <div>
                            <x-input-label for="nama" :value="__('Nama Lengkap')" />
                            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full"
                                          :value="old('nama')" required placeholder="Nama lengkap nasabah" />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>

                        {{-- No HP --}}
                        <div>
                            <x-input-label for="no_hp" :value="__('Nomor Handphone / WhatsApp')" />
                            <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full"
                                          :value="old('no_hp')" required placeholder="Contoh: 081234567890" />
                            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                        </div>

                        {{-- Wilayah RW & RT --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nomor_rw" :value="__('Nomor RW')" />
                                <x-text-input id="nomor_rw" name="nomor_rw" type="text" class="mt-1 block w-full"
                                              :value="old('nomor_rw')" required placeholder="Contoh: 05" />
                                <x-input-error :messages="$errors->get('nomor_rw')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="nomor_rt" :value="__('Nomor RT (Opsional)')" />
                                <x-text-input id="nomor_rt" name="nomor_rt" type="text" class="mt-1 block w-full"
                                              :value="old('nomor_rt')" placeholder="Contoh: 02" />
                                <x-input-error :messages="$errors->get('nomor_rt')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Alamat Lengkap --}}
                        <div>
                            <x-input-label for="alamat" :value="__('Alamat Lengkap (Opsional)')" />
                            <textarea id="alamat" name="alamat" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                      placeholder="Nama jalan, nomor rumah, dll.">{{ old('alamat') }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>

                        {{-- Kader Pembina & Saldo Awal --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="kader_id" :value="__('Kader Pembina (Opsional)')" />
                                <select id="kader_id" name="kader_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                    <option value="">-- Didaftarkan oleh Admin --</option>
                                    @foreach ($kaders as $kader)
                                        <option value="{{ $kader->id }}" @selected(old('kader_id') == $kader->id)>
                                            {{ $kader->name }} ({{ '@' . $kader->username }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('kader_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="saldo_awal" :value="__('Saldo Awal (Rp, Opsional)')" />
                                <x-text-input id="saldo_awal" name="saldo_awal" type="number" step="100" min="0" class="mt-1 block w-full"
                                              :value="old('saldo_awal', 0)" />
                                <x-input-error :messages="$errors->get('saldo_awal')" class="mt-2" />
                            </div>
                        </div>

                        <h3 class="text-md font-semibold text-gray-700 border-b pb-2 pt-4">Akun Login Nasabah</h3>
                        <p class="text-xs text-gray-500 -mt-3">Jika dikosongkan, sistem akan membuatkan username otomatis dan password default dari 6 digit terakhir NIK.</p>

                        {{-- Username --}}
                        <div>
                            <x-input-label for="username" :value="__('Username (Opsional)')" />
                            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full"
                                          :value="old('username')" placeholder="Kosongkan untuk auto-generate" />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Email (Opsional)')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                          :value="old('email')" placeholder="nasabah@example.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Password --}}
                        <div>
                            <x-input-label for="password" :value="__('Password (Opsional)')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                          placeholder="Kosongkan untuk default (6 digit terakhir NIK)" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        {{-- Status Aktif --}}
                        <div class="flex items-center gap-3">
                            <input id="is_active" name="is_active" type="checkbox" value="1"
                                   class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"
                                   @checked(old('is_active', '1') === '1') />
                            <x-input-label for="is_active" :value="__('Akun Langsung Aktif')" class="mb-0" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.nasabah.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <x-primary-button class="bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 focus:ring-emerald-500">
                                Simpan Nasabah
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

