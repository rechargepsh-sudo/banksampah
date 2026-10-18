<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.kategori-sampah.index') }}"
               class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Kategori Sampah</h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftarkan jenis sampah baru beserta harga per kg acuan penimbangan.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.kategori-sampah.store') }}">
                        @csrf

                        {{-- Kode & Nama Kategori --}}
                        <div class="mb-5 grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div class="sm:col-span-1">
                                <x-input-label for="kode" value="Kode Barang" />
                                <x-text-input id="kode"
                                              name="kode"
                                              type="text"
                                              class="mt-1 block w-full font-mono uppercase"
                                              :value="old('kode')"
                                              placeholder="1A, 2D, 3F"
                                              maxlength="10" />
                                <x-input-error :messages="$errors->get('kode')" class="mt-1" />
                            </div>
                            <div class="sm:col-span-3">
                                <x-input-label for="nama_kategori" value="Nama Kategori Sampah *" />
                                <x-text-input id="nama_kategori"
                                              name="nama_kategori"
                                              type="text"
                                              class="mt-1 block w-full"
                                              :value="old('nama_kategori')"
                                              placeholder="Contoh: KARDUS, ALUMUNIUM"
                                              required />
                                <x-input-error :messages="$errors->get('nama_kategori')" class="mt-1" />
                            </div>
                        </div>

                        {{-- Harga & Satuan --}}
                        <div class="mb-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="harga_per_kg" value="Harga per Satuan (Rp) *" />
                                <div class="mt-1 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm">Rp</span>
                                    </div>
                                    <x-text-input id="harga_per_kg"
                                                  name="harga_per_kg"
                                                  type="number"
                                                  class="block w-full pl-9"
                                                  :value="old('harga_per_kg', '0')"
                                                  min="0"
                                                  step="100"
                                                  placeholder="0"
                                                  required />
                                </div>
                                <x-input-error :messages="$errors->get('harga_per_kg')" class="mt-1" />
                                <p class="text-xs text-gray-500 mt-1">Harga acuan per satuan berat/volume.</p>
                            </div>

                            <div>
                                <x-input-label for="satuan" value="Satuan *" />
                                <select id="satuan"
                                        name="satuan"
                                        class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm text-sm"
                                        required>
                                    <option value="">-- Pilih Satuan --</option>
                                    <option value="kg" {{ old('satuan', 'kg') === 'kg' ? 'selected' : '' }}>kg (Kilogram)</option>
                                    <option value="liter" {{ old('satuan') === 'liter' ? 'selected' : '' }}>liter</option>
                                    <option value="pcs" {{ old('satuan') === 'pcs' ? 'selected' : '' }}>pcs (Satuan Buah)</option>
                                </select>
                                <x-input-error :messages="$errors->get('satuan')" class="mt-1" />
                            </div>
                        </div>

                        {{-- Status Aktif --}}
                        <div class="mb-6">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', '1') === '1' ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Aktifkan Kategori Ini</span>
                                    <p class="text-xs text-gray-500">Kategori yang aktif akan muncul sebagai pilihan di form pencatatan setor sampah oleh Kader.</p>
                                </div>
                            </label>
                        </div>

                        {{-- Divider & Info --}}
                        <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800">
                            <strong class="block mb-1 font-semibold">💡 Contoh Data Acuan Harga Pasar:</strong>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-0.5 mt-1">
                                <span>Kardus / Karton Box — <strong>Rp 2.500/kg</strong></span>
                                <span>Botol Plastik PET — <strong>Rp 3.000/kg</strong></span>
                                <span>Kaleng Aluminium — <strong>Rp 12.000/kg</strong></span>
                                <span>Minyak Jelantah — <strong>Rp 6.000/liter</strong></span>
                                <span>Logam Besi/Rongsok — <strong>Rp 4.500/kg</strong></span>
                                <span>Kertas HVS — <strong>Rp 2.200/kg</strong></span>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.kategori-sampah.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <x-primary-button type="submit">
                                + Simpan Kategori Baru
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

