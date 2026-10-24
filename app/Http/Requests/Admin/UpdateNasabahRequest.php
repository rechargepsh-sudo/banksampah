<?php

namespace App\Http\Requests\Admin;

use App\Models\Nasabah;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNasabahRequest extends FormRequest
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
        $nasabahParam = $this->route('nasabah');
        $nasabah = $nasabahParam instanceof Nasabah
            ? $nasabahParam
            : Nasabah::find($nasabahParam);

        $nasabahId = $nasabah?->id;
        $userId = $nasabah?->user_id;

        return [
            'nik' => [
                'required',
                'string',
                'digits:16',
                Rule::unique('nasabah', 'nik')->ignore($nasabahId),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:15'],
            'kecamatan' => ['nullable', 'string', 'max:60'],
            'kelurahan' => ['nullable', 'string', 'max:60'],
            'nomor_rw' => ['required', 'string', 'max:5'],
            'nomor_rt' => ['nullable', 'string', 'max:5'],
            'alamat' => ['nullable', 'string'],
            'tanggal_bergabung' => ['nullable', 'date'],
            'kader_id' => ['nullable', 'exists:users,id'],
            'username' => [
                'nullable',
                'string',
                'alpha_dash',
                'max:50',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($userId),
            ],
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
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username sudah digunakan.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
        ];
    }
}
