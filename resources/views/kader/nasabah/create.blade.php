<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('kader.nasabah.index') }}"
               class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    + Pendaftaran Nasabah Baru
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftarkan warga lingkungan untuk membuka buku tabungan bank sampah digital.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('error'))
                <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Info Card: Auto-generate Akun --}}
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-5 text-white shadow-md flex items-start gap-4">
                <div class="p-2.5 rounded-lg bg-white/20 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-xs space-y-1">
                    <p class="font-bold text-sm">Sistem Otomatis Pembuatan Akun Warga</p>
                    <p class="text-emerald-50 leading-relaxed">
                        Saat Kader mendaftarkan nasabah, sistem akan langsung meng-generate akun login mandiri bagi warga.
                        Nasabah dapat langsung masuk di web menggunakan <strong>16 digit NIK</strong> dengan kata sandi awal <strong>6 digit terakhir NIK</strong>.
                    </p>
                </div>
            </div>

            {{-- Form Pendaftaran --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <form method="POST" action="{{ route('kader.nasabah.store') }}" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    {{-- Bagian 1: Data Identitas Warga --}}
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 inline-flex items-center justify-center text-xs">1</span>
                            Identitas Kependudukan & Kontak
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- NIK --}}
                            <div>
                                <x-input-label for="nik" value="Nomor Induk Kependudukan (NIK 16 Digit) *" />
                                <x-text-input id="nik"
                                              name="nik"
                                              type="text"
                                              class="mt-1 block w-full font-mono text-sm"
                                              :value="old('nik')"
                                              maxlength="16"
                                              placeholder="3201xxxxxxxxxxxx"
                                              required />
                                <x-input-error :messages="$errors->get('nik')" class="mt-1" />
                                <p class="text-[11px] text-gray-500 mt-1">NIK digunakan nasabah untuk login mandiri dan acuan password awal.</p>
                            </div>

                            {{-- Nama Lengkap --}}
                            <div>
                                <x-input-label for="nama" value="Nama Lengkap Nasabah *" />
                                <x-text-input id="nama"
                                              name="nama"
                                              type="text"
                                              class="mt-1 block w-full text-sm"
                                              :value="old('nama')"
                                              placeholder="Contoh: Dewi Lestari"
                                              required />
                                <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                            </div>

                            {{-- No HP / WhatsApp --}}
                            <div>
                                <x-input-label for="no_hp" value="Nomor HP / WhatsApp *" />
                                <x-text-input id="no_hp"
                                              name="no_hp"
                                              type="text"
                                              class="mt-1 block w-full text-sm font-mono"
                                              :value="old('no_hp')"
                                              placeholder="0812xxxxxxxx"
                                              required />
                                <x-input-error :messages="$errors->get('no_hp')" class="mt-1" />
                            </div>

                            {{-- Nomor RW & RT --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="nomor_rw" value="Nomor RW *" />
                                    <x-text-input id="nomor_rw"
                                                  name="nomor_rw"
                                                  type="text"
                                                  class="mt-1 block w-full text-sm text-center font-mono"
                                                  :value="old('nomor_rw', '05')"
                                                  maxlength="5"
                                                  placeholder="05"
                                                  required />
                                    <x-input-error :messages="$errors->get('nomor_rw')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="nomor_rt" value="Nomor RT" />
                                    <x-text-input id="nomor_rt"
                                                  name="nomor_rt"
                                                  type="text"
                                                  class="mt-1 block w-full text-sm text-center font-mono"
                                                  :value="old('nomor_rt')"
                                                  maxlength="5"
                                                  placeholder="02" />
                                    <x-input-error :messages="$errors->get('nomor_rt')" class="mt-1" />
                                </div>
                            </div>

                            {{-- Alamat Domisili --}}
                            <div class="sm:col-span-2">
                                <x-input-label for="alamat" value="Alamat Domisili" />
                                <textarea id="alamat"
                                          name="alamat"
                                          rows="2"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                                          placeholder="Nama jalan, nomor rumah, atau patokan tempat tinggal">{{ old('alamat') }}</textarea>
                                <x-input-error :messages="$errors->get('alamat')" class="mt-1" />
                            </div>

                            {{-- Tanggal Bergabung --}}
                            <div class="sm:col-span-2">
                                <x-input-label for="tanggal_bergabung" value="Tanggal Bergabung Nasabah" />
                                <x-text-input id="tanggal_bergabung"
                                              name="tanggal_bergabung"
                                              type="date"
                                              class="mt-1 block w-full sm:w-1/2 text-sm"
                                              :value="old('tanggal_bergabung', date('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('tanggal_bergabung')" class="mt-1" />
                                <p class="text-[11px] text-gray-500 mt-1">Tanggal resmi warga terdaftar sebagai nasabah bank sampah.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Bagian 2: Kredensial Akun Login (Opsional / Otomatis) --}}
                    <div class="pt-2">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 inline-flex items-center justify-center text-xs">2</span>
                            Akun Login Mandiri Warga <span class="text-xs font-normal text-gray-400 capitalize">(Opsional / Auto-Generate)</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            {{-- Username --}}
                            <div>
                                <x-input-label for="username" value="Username Khusus" />
                                <x-text-input id="username"
                                              name="username"
                                              type="text"
                                              class="mt-1 block w-full text-sm font-mono"
                                              :value="old('username')"
                                              placeholder="Otomatis jika kosong" />
                                <x-input-error :messages="$errors->get('username')" class="mt-1" />
                                <p class="text-[11px] text-gray-400 mt-1">Format default: nsb_namadepan_4digitnik</p>
                            </div>

                            {{-- Password --}}
                            <div>
                                <x-input-label for="password" value="Password Awal" />
                                <x-text-input id="password"
                                              name="password"
                                              type="text"
                                              class="mt-1 block w-full text-sm font-mono"
                                              placeholder="Default: 6 digit akhir NIK" />
                                <x-input-error :messages="$errors->get('password')" class="mt-1" />
                                <p class="text-[11px] text-gray-400 mt-1">Minimal 6 karakter jika diisi manual</p>
                            </div>

                            {{-- Email --}}
                            <div>
                                <x-input-label for="email" value="Email Warga" />
                                <x-text-input id="email"
                                              name="email"
                                              type="email"
                                              class="mt-1 block w-full text-sm"
                                              :value="old('email')"
                                              placeholder="opsional@email.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-between pt-5 border-t border-gray-200">
                        <a href="{{ route('kader.nasabah.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                            Batal
                        </a>
                        <x-primary-button class="bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700">
                            + Daftarkan Nasabah Baru
                        </x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

