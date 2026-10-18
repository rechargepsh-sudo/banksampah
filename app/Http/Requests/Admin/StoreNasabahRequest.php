<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreNasabahRequest extends FormRequest
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
            'nik' => ['required', 'string', 'digits:16', 'unique:nasabah,nik'],
            'nama' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:15'],
            'nomor_rw' => ['required', 'string', 'max:5'],
            'nomor_rt' => ['nullable', 'string', 'max:5'],
            'alamat' => ['nullable', 'string'],
            'kader_id' => ['nullable', 'exists:users,id'],
            'saldo_awal' => ['nullable', 'numeric', 'min:0'],
            'username' => ['nullable', 'string', 'alpha_dash', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
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
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus berjumlah tepat 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar di sistem.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.max' => 'Nomor HP maksimal 15 karakter.',
            'nomor_rw.required' => 'Nomor RW wajib diisi.',
            'nomor_rw.max' => 'Nomor RW maksimal 5 karakter.',
            'kader_id.exists' => 'Kader pembina yang dipilih tidak valid.',
            'saldo_awal.numeric' => 'Saldo awal harus berupa angka.',
            'saldo_awal.min' => 'Saldo awal tidak boleh kurang dari 0.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username sudah digunakan.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ];
    }
}
