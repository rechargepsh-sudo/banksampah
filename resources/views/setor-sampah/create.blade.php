<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('setor-sampah.index') }}" class="text-gray-500 hover:text-gray-700">
                ← Riwayat Setoran
            </a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pencatatan Setoran Sampah (Timbangan)
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Error Flash --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-800 rounded-md text-sm">
                    <p class="font-bold mb-1">Terjadi kesalahan pada data yang Anda input:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('setor-sampah.store') }}"
                          x-data="setorForm({{ Js::from($kategoriList) }})" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-200">
                            {{-- Pilih Nasabah --}}
                            <div>
                                <x-input-label for="nasabah_id" :value="__('Pilih Nasabah Warga')" />
                                <select id="nasabah_id" name="nasabah_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                    <option value="">-- Pilih Nasabah Pemilik Tabungan --</option>
                                    @foreach ($nasabahList as $nasabah)
                                        <option value="{{ $nasabah->id }}" @selected(old('nasabah_id', $selectedNasabahId) == $nasabah->id)>
                                            {{ $nasabah->nama }} — NIK: {{ $nasabah->nik }} (RW {{ $nasabah->nomor_rw }}/RT {{ $nasabah->nomor_rt ?? '-' }}) [Saldo: Rp {{ number_format($nasabah->saldo_aktif, 0, ',', '.') }}]
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('nasabah_id')" class="mt-2" />
                            </div>

                            {{-- Tanggal Setor --}}
                            <div>
                                <x-input-label for="tanggal_setor" :value="__('Tanggal & Waktu Setor')" />
                                <x-text-input id="tanggal_setor" name="tanggal_setor" type="datetime-local" class="mt-1 block w-full"
                                              :value="old('tanggal_setor', now()->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('tanggal_setor')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Tabel Dinamis Timbangan Sampah --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800">Rincian Timbangan Sampah Terpilah</h3>
                                    <p class="text-xs text-gray-500">Pilih kategori sampah, masukkan berat (kg), sistem otomatis mengkalkulasi nominal saldo.</p>
                                </div>
                                <button type="button" @click="addItem()"
                                        class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-md hover:bg-emerald-500 transition">
                                    + Tambah Baris Sampah
                                </button>
                            </div>

                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50 text-gray-600">
                                        <tr>
                                            <th class="px-4 py-3 text-left w-12">#</th>
                                            <th class="px-4 py-3 text-left">Kategori Sampah</th>
                                            <th class="px-4 py-3 text-right w-36">Harga / kg</th>
                                            <th class="px-4 py-3 text-right w-36">Berat (kg)</th>
                                            <th class="px-4 py-3 text-right w-44">Subtotal (Rp)</th>
                                            <th class="px-4 py-3 text-center w-16">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-400 font-mono text-xs" x-text="index + 1"></td>
                                                <td class="px-4 py-3">
                                                    <select :name="'items[' + index + '][kategori_sampah_id]'"
                                                            x-model="item.kategori_sampah_id"
                                                            @change="updateHarga(index)"
                                                            required
                                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs">
                                                        <option value="">-- Pilih Jenis Sampah --</option>
                                                        <template x-for="kat in masterKategori" :key="kat.id">
                                                            <option :value="kat.id" x-text="(kat.kode ? '[' + kat.kode + '] ' : '') + kat.nama_kategori + ' (Rp ' + formatRupiah(kat.harga_per_kg) + '/' + kat.satuan + ')'"></option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3 text-right text-gray-600 font-mono text-xs"
                                                    x-text="'Rp ' + formatRupiah(item.harga_satuan)">
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <input type="number"
                                                           :name="'items[' + index + '][berat]'"
                                                           x-model.number="item.berat"
                                                           step="0.01" min="0.01" required
                                                           placeholder="0.00"
                                                           class="w-full text-right rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs font-mono" />
                                                </td>
                                                <td class="px-4 py-3 text-right font-semibold text-emerald-700 font-mono text-xs"
                                                    x-text="'Rp ' + formatRupiah(hitungSubtotal(item))">
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" @click="removeItem(index)"
                                                            :disabled="items.length <= 1"
                                                            :class="items.length <= 1 ? 'opacity-30 cursor-not-allowed text-gray-400' : 'text-red-500 hover:text-red-700'"
                                                            class="p-1">
                                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Ringkasan Total & Catatan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                            <div>
                                <x-input-label for="catatan" :value="__('Catatan Tambahan (Opsional)')" />
                                <textarea id="catatan" name="catatan" rows="3"
                                          placeholder="Contoh: Kondisi sampah bersih dan sudah dipilah rapi"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('catatan') }}</textarea>
                            </div>

                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-5 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm text-emerald-900">
                                        <span>Total Jenis Sampah:</span>
                                        <span class="font-semibold" x-text="items.length + ' jenis'"></span>
                                    </div>
                                    <div class="flex justify-between text-sm text-emerald-900">
                                        <span>Total Berat Keseluruhan:</span>
                                        <span class="font-semibold font-mono" x-text="totalBerat().toFixed(2) + ' kg'"></span>
                                    </div>
                                    <div class="border-t border-emerald-300 pt-2 flex justify-between items-center">
                                        <span class="text-base font-bold text-emerald-950">Total Nominal Tabungan:</span>
                                        <span class="text-xl font-bold text-emerald-700 font-mono" x-text="'Rp ' + formatRupiah(totalNominal())"></span>
                                    </div>
                                </div>
                                <p class="text-xs text-emerald-700 mt-3 italic">* Saldo nasabah akan langsung bertambah sebesar total nominal di atas.</p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('setor-sampah.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                                Batal
                            </a>
                            <x-primary-button class="bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 focus:ring-emerald-500">
                                Simpan & Cetak Struk Setoran
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine JS Script --}}
    <script>
        function setorForm(masterKategori) {
            return {
                masterKategori: masterKategori,
                items: [
                    { kategori_sampah_id: '', harga_satuan: 0, berat: '' }
                ],
                addItem() {
                    this.items.push({
                        kategori_sampah_id: '',
                        harga_satuan: 0,
                        berat: ''
                    });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                updateHarga(index) {
                    const selectedId = this.items[index].kategori_sampah_id;
                    const kategori = this.masterKategori.find(k => k.id == selectedId);
                    this.items[index].harga_satuan = kategori ? parseFloat(kategori.harga_per_kg) : 0;
                },
                hitungSubtotal(item) {
                    const berat = parseFloat(item.berat) || 0;
                    const harga = parseFloat(item.harga_satuan) || 0;
                    return Math.round(berat * harga);
                },
                totalBerat() {
                    return this.items.reduce((sum, item) => sum + (parseFloat(item.berat) || 0), 0);
                },
                totalNominal() {
                    return this.items.reduce((sum, item) => sum + this.hitungSubtotal(item), 0);
                },
                formatRupiah(angka) {
                    const val = Math.round(parseFloat(angka) || 0);
                    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            }
        }
    </script>
</x-app-layout>

