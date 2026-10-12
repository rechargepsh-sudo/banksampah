<?php

namespace App\Http\Requests\Kader;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterNasabahRequest extends FormRequest
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
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'password' => ['nullable', 'string', 'min:6'],
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
            'nik.digits' => 'NIK harus berjumlah tepat 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar di sistem.',
            'nama.required' => 'Nama lengkap nasabah wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.max' => 'Nomor HP maksimal 15 karakter.',
            'nomor_rw.required' => 'Nomor RW wajib diisi.',
            'nomor_rw.max' => 'Nomor RW maksimal 5 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ];
    }
}
