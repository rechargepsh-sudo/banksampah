<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransaksiSetorRequest extends FormRequest
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
        return [
            'nasabah_id' => ['required', 'integer', 'exists:nasabah,id'],
            'tanggal_setor' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.kategori_sampah_id' => ['required', 'integer', 'exists:kategori_sampah,id'],
            'items.*.berat' => ['required', 'numeric', 'gt:0'],
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
            'nasabah_id.required' => 'Nasabah wajib dipilih.',
            'nasabah_id.exists' => 'Nasabah tidak ditemukan.',
            'items.required' => 'Item setoran sampah wajib diisi minimal 1.',
            'items.min' => 'Item setoran sampah wajib diisi minimal 1 item.',
            'items.*.kategori_sampah_id.required' => 'Kategori sampah wajib dipilih.',
            'items.*.kategori_sampah_id.exists' => 'Kategori sampah tidak valid.',
            'items.*.berat.required' => 'Berat timbangan wajib diisi.',
            'items.*.berat.numeric' => 'Berat timbangan harus berupa angka.',
            'items.*.berat.gt' => 'Berat timbangan harus lebih besar dari 0.',
        ];
    }
}
