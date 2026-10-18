<?php

namespace App\Http\Requests\Auth;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('login') && ! $this->has('email')) {
            $this->merge(['email' => $this->input('login')]);
        } elseif ($this->has('email') && ! $this->has('login')) {
            $this->merge(['login' => $this->input('email')]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
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
            'login.required' => 'Username, Email, atau NIK wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = (string) ($this->input('login') ?? $this->input('email'));
        $password = (string) $this->input('password');

        // Cari user berdasarkan username, email, atau NIK nasabah
        $user = User::where('username', $loginInput)
            ->orWhere('email', $loginInput)
            ->first();

        if (! $user) {
            $nasabah = Nasabah::where('nik', $loginInput)->first();
            if ($nasabah) {
                $user = $nasabah->user;
            }
        }

        $isValidPassword = false;
        if ($user) {
            $user->loadMissing('nasabah');
            if (Hash::check($password, $user->password)) {
                $isValidPassword = true;
            } elseif ($user->role === 'nasabah' && $user->nasabah && $password === substr($user->nasabah->nik, -6)) {
                $isValidPassword = true;
            }
        }

        if (! $user || ! $isValidPassword) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
                'email' => trans('auth.failed'),
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'login' => 'Akun Anda berstatus non-aktif. Silakan hubungi Kader atau Admin.',
                'email' => 'Akun Anda berstatus non-aktif. Silakan hubungi Kader atau Admin.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $login = (string) ($this->input('login') ?? $this->input('email') ?? '');

        return Str::transliterate(Str::lower($login).'|'.$this->ip());
    }
}
