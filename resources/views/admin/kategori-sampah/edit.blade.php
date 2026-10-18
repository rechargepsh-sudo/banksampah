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
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Kategori: <span class="text-emerald-700">{{ $kategoriSampah->nama_kategori }}</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui nama, harga, atau status kategori sampah ini.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Info Usage --}}
                    @if ($jumlahDipakai > 0)
                        <div class="mb-5 p-3.5 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800 flex items-start gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <strong>Perhatian:</strong> Kategori ini sudah digunakan dalam <strong>{{ $jumlahDipakai }} transaksi setor</strong>.
                                Mengubah harga per kg hanya akan berlaku untuk transaksi <strong>baru</strong> setelah perubahan ini disimpan.
                                Riwayat transaksi lama tidak akan terpengaruh karena harga sudah tersimpan per baris transaksi.
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.kategori-sampah.update', $kategoriSampah) }}">
                        @csrf
                        @method('PUT')

                        {{-- Kode & Nama Kategori --}}
                        <div class="mb-5 grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div class="sm:col-span-1">
                                <x-input-label for="kode" value="Kode Barang" />
                                <x-text-input id="kode"
                                              name="kode"
                                              type="text"
                                              class="mt-1 block w-full font-mono uppercase"
                                              :value="old('kode', $kategoriSampah->kode)"
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
                                              :value="old('nama_kategori', $kategoriSampah->nama_kategori)"
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
                                                  :value="old('harga_per_kg', $kategoriSampah->harga_per_kg)"
                                                  min="0"
                                                  step="100"
                                                  required />
                                </div>
                                <x-input-error :messages="$errors->get('harga_per_kg')" class="mt-1" />
                                @if ($jumlahDipakai > 0)
                                    <p class="text-xs text-amber-600 mt-1">
                                        Harga saat ini: <strong>Rp {{ number_format((float) $kategoriSampah->harga_per_kg, 0, ',', '.') }}/{{ $kategoriSampah->satuan }}</strong>
                                    </p>
                                @endif
                            </div>

                            <div>
                                <x-input-label for="satuan" value="Satuan *" />
                                <select id="satuan"
                                        name="satuan"
                                        class="mt-1 block w-full border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm text-sm"
                                        {{ $jumlahDipakai > 0 ? 'disabled' : '' }}
                                        required>
                                    <option value="kg" {{ old('satuan', $kategoriSampah->satuan) === 'kg' ? 'selected' : '' }}>kg (Kilogram)</option>
                                    <option value="liter" {{ old('satuan', $kategoriSampah->satuan) === 'liter' ? 'selected' : '' }}>liter</option>
                                    <option value="pcs" {{ old('satuan', $kategoriSampah->satuan) === 'pcs' ? 'selected' : '' }}>pcs (Satuan Buah)</option>
                                </select>
                                @if ($jumlahDipakai > 0)
                                    {{-- Hidden fallback jika select disabled --}}
                                    <input type="hidden" name="satuan" value="{{ $kategoriSampah->satuan }}">
                                    <p class="text-xs text-gray-400 mt-1">Satuan tidak dapat diubah karena sudah ada transaksi terkait.</p>
                                @endif
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
                                       {{ old('is_active', $kategoriSampah->is_active ? '1' : '0') === '1' ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Kategori Aktif</span>
                                    <p class="text-xs text-gray-500">Jika dinonaktifkan, kategori ini tidak akan muncul di form setor sampah baru.</p>
                                </div>
                            </label>
                        </div>

                        {{-- Meta Info --}}
                        <div class="mb-6 p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-600 grid grid-cols-2 gap-2">
                            <div>
                                <span class="font-medium text-gray-700">ID Kategori:</span> #{{ $kategoriSampah->id }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Dibuat:</span> {{ $kategoriSampah->created_at->format('d M Y H:i') }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Dipakai Transaksi:</span>
                                <span class="{{ $jumlahDipakai > 0 ? 'text-amber-700 font-semibold' : 'text-gray-500' }}">
                                    {{ $jumlahDipakai }} kali
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Terakhir Update:</span> {{ $kategoriSampah->updated_at->diffForHumans() }}
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.kategori-sampah.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition ease-in-out duration-150">
                                Kembali
                            </a>
                            <x-primary-button type="submit">
                                Simpan Perubahan
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Danger Zone: Hapus --}}
            @if ($jumlahDipakai === 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-red-200">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-red-700 mb-1">⚠️ Zona Berbahaya</h3>
                        <p class="text-xs text-gray-600 mb-4">
                            Kategori ini belum pernah digunakan dalam transaksi. Anda dapat menghapusnya secara permanen.
                            Tindakan ini <strong>tidak dapat dibatalkan</strong>.
                        </p>
                        <form method="POST"
                              action="{{ route('admin.kategori-sampah.destroy', $kategoriSampah) }}"
                              x-data="{}"
                              @submit.prevent="if(confirm('Hapus permanen kategori \"{{ addslashes($kategoriSampah->nama_kategori) }}\"?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

