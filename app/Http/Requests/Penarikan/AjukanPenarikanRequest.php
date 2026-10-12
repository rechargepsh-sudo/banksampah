<?php

namespace App\Http\Requests\Penarikan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AjukanPenarikanRequest extends FormRequest
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
            'jumlah_penarikan' => ['required', 'numeric', 'gt:0'],
            'metode_penarikan' => ['required', 'in:tunai,transfer'],
            'catatan' => ['nullable', 'string', 'max:500'],
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
            'jumlah_penarikan.required' => 'Jumlah penarikan wajib diisi.',
            'jumlah_penarikan.numeric' => 'Jumlah penarikan harus berupa angka.',
            'jumlah_penarikan.gt' => 'Jumlah penarikan harus lebih dari 0.',
            'metode_penarikan.required' => 'Metode penarikan wajib dipilih.',
            'metode_penarikan.in' => 'Metode penarikan hanya boleh tunai atau transfer.',
        ];
    }
}
