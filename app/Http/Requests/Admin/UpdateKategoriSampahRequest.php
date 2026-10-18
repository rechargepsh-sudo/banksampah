<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriSampahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $kategoriId = $this->route('kategori_sampah')?->id;

        return [
            'kode' => ['nullable', 'string', 'max:10'],
            'nama_kategori' => [
                'required',
                'string',
                'max:150',
                Rule::unique('kategori_sampah', 'nama_kategori')->ignore($kategoriId),
            ],
            'harga_per_kg' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'satuan' => ['required', 'string', 'in:kg,liter,pcs'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori sampah wajib diisi.',
            'nama_kategori.max' => 'Nama kategori sampah maksimal 100 karakter.',
            'nama_kategori.unique' => 'Nama kategori sampah sudah digunakan. Gunakan nama yang berbeda.',
            'harga_per_kg.required' => 'Harga per satuan wajib diisi.',
            'harga_per_kg.numeric' => 'Harga harus berupa angka.',
            'harga_per_kg.min' => 'Harga tidak boleh negatif.',
            'satuan.required' => 'Satuan wajib dipilih.',
            'satuan.in' => 'Satuan harus salah satu dari: kg, liter, atau pcs.',
        ];
    }
}
