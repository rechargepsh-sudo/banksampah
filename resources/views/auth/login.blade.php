<x-guest-layout>
    <!-- Header Form -->
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 mb-3 shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900">Masuk ke Akun Anda</h2>
        <p class="text-xs text-gray-500 mt-1">Sistem Informasi Bank Sampah Digital RW / RT</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false }">
        @csrf

        <!-- Username, Email, atau NIK -->
        <div>
            <label for="login" class="block font-medium text-sm text-gray-700">
                Username / Email / NIK <span class="text-xs font-normal text-emerald-600">(16 Digit)</span>
            </label>
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input id="login"
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                       type="text"
                       name="login"
                       :value="old('login', old('email'))"
                       required
                       autofocus
                       autocomplete="username"
                       placeholder="Contoh: 3201015502900001 / nasabah_dewi / admin" />
            </div>
            <x-input-error :messages="$errors->get('login') ?: $errors->get('email')" class="mt-2" />
            <p class="mt-1.5 text-xs text-gray-500 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Nasabah dapat login menggunakan <strong>16 digit NIK</strong>, Username, atau Email.
            </p>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex items-center justify-between">
                <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs text-emerald-600 hover:text-emerald-700 hover:underline" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>

            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password"
                       class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                       :type="showPassword ? 'text' : 'password'"
                       name="password"
                       required
                       autocomplete="current-password"
                       placeholder="••••••••" />
                <button type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                <span class="ms-2 text-xs text-gray-600">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <!-- Tombol Masuk -->
        <div class="mt-5">
            <button type="submit"
                    class="w-full inline-flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Masuk ke Sistem
            </button>
        </div>
    </form>

    <!-- Info Akun Uji Coba / Quick Fill -->
    <div class="mt-6 pt-5 border-t border-gray-200" x-data="{
        fill(username, password) {
            document.getElementById('login').value = username;
            document.getElementById('password').value = password;
        }
    }">
        <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2 flex items-center justify-between">
            <span>Uji Coba Cepat (Quick Fill)</span>
            <span class="text-[10px] text-gray-400 font-normal">Klik untuk mengisi</span>
        </p>

        <div class="grid grid-cols-2 gap-1.5 text-xs">
            <!-- Nasabah via NIK -->
            <button type="button"
                    @click="fill('3201015502900001', 'password')"
                    class="text-left p-2 rounded-md border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                <div class="font-medium text-gray-900 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Nasabah Dewi
                </div>
                <div class="text-[11px] text-gray-500 font-mono">NIK: ...0001</div>
            </button>

            <!-- Nasabah via Username -->
            <button type="button"
                    @click="fill('nasabah_agus', 'password')"
                    class="text-left p-2 rounded-md border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                <div class="font-medium text-gray-900 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Nasabah Agus
                </div>
                <div class="text-[11px] text-gray-500 font-mono">nasabah_agus</div>
            </button>

            <!-- Kader -->
            <button type="button"
                    @click="fill('kader_siti', 'password')"
                    class="text-left p-2 rounded-md border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                <div class="font-medium text-gray-900 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Kader Siti
                </div>
                <div class="text-[11px] text-gray-500 font-mono">kader_siti</div>
            </button>

            <!-- Admin -->
            <button type="button"
                    @click="fill('admin', 'password')"
                    class="text-left p-2 rounded-md border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition">
                <div class="font-medium text-gray-900 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span> Admin
                </div>
                <div class="text-[11px] text-gray-500 font-mono">admin</div>
            </button>
        </div>

        <div class="mt-3 bg-gray-50 rounded-lg p-2.5 text-[11px] text-gray-600 border border-gray-200 flex items-start gap-2">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <strong>Petunjuk Password Nasabah:</strong> Nasabah dapat menggunakan password yang didaftarkan Kader atau <strong>6 digit terakhir NIK</strong> sebagai sandi awal.
            </div>
        </div>
    </div>
</x-guest-layout>
