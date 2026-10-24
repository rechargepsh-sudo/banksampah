<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bank Sampah Digital RW - Solusi Pengelolaan Tabungan Sampah Lingkungan</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans flex flex-col min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/" class="flex items-center gap-3">
                    <x-application-logo class="h-11 w-auto object-contain" />
                </a>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-emerald-600 text-xs font-semibold rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition shadow-sm">
                        Dashboard Utama &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm shadow-emerald-200 transition">
                        Masuk ke Akun
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="flex-grow">
        <div class="relative overflow-hidden bg-gradient-to-b from-emerald-50 via-teal-50/40 to-slate-50 py-16 sm:py-24">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Digital Tabungan Sampah RW Mandiri & Real-Time
                </span>

                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">
                    Ubah Sampah Terpilah Menjadi <br class="hidden sm:inline" />
                    <span class="text-emerald-600">Tabungan Berkah & Lingkungan Bersih</span>
                </h1>

                <p class="mt-4 sm:mt-6 text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    Mendigitalkan seluruh alur pencatatan sampah daur ulang tingkat warga: pendaftaran nasabah, penimbangan otomatis oleh kader pos, cek saldo mandiri dengan NIK, hingga penarikan dana langsung.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-semibold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-300 transition duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Masuk Menggunakan NIK / Username
                    </a>
                    <a href="#fitur" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition duration-150">
                        Pelajari Alur Kerja
                    </a>
                </div>

                <!-- Info Banner Login -->
                <div class="mt-10 max-w-xl mx-auto bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-emerald-200/70 shadow-sm text-left flex items-start gap-3">
                    <div class="p-2 rounded-lg bg-emerald-100 text-emerald-700 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-xs text-gray-700 leading-relaxed">
                        <strong class="text-gray-900 block font-semibold mb-0.5">Kemudahan Akses untuk Warga:</strong>
                        Nasabah yang telah didaftarkan oleh Kader Pos RW dapat langsung masuk menggunakan <strong>16 digit NIK</strong> dengan kata sandi awal <strong>6 digit terakhir NIK</strong> (atau password yang diberikan Kader).
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Pilar Fitur -->
        <div id="fitur" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-1">Keunggulan Sistem</h2>
                <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">Solusi Lengkap Bank Sampah Digital</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-md shadow-slate-100 hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Penimbangan & Setor Cepat</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Kader pos RW menimbang sampah terpilah (kardus, botol plastik, kaleng, dll) dan sistem langsung mengalkulasi nominal serta menambahkan saldo secara atomik.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-md shadow-slate-100 hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Saldo & Mutasi Real-Time</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Nasabah dapat login mandiri di gawai kapan saja menggunakan 16 digit NIK untuk melihat saldo aktif tabungan dan riwayat mutasi masuk/keluar secara transparan.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-md shadow-slate-100 hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Tarik Saldo Terpantau</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Nasabah dapat mengajukan permohonan penarikan dana langsung dari browser. Kader memvalidasi permohonan, mencetak kwitansi fisik, dan menyerahkan uang tunai.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-2">
            <div>
                &copy; {{ date('Y') }} Sistem Informasi Bank Sampah Digital RW / RT. Seluruh hak cipta dilindungi.
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="hover:text-emerald-600 font-medium transition">Halaman Login</a>
                <span>&bull;</span>
                <span>Laravel 13 & Sanctum API</span>
            </div>
        </div>
    </footer>
</body>
</html>
