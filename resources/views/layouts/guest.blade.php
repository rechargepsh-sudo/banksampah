<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Bank Sampah Digital') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-slate-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-8 px-4 bg-gradient-to-br from-emerald-50 via-teal-50 to-slate-100">
            <div class="mb-2">
                <a href="/" class="flex items-center gap-2 group">
                    <x-application-logo class="h-16 w-auto object-contain drop-shadow-sm group-hover:scale-105 transition" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-4 px-6 py-6 bg-white shadow-xl shadow-slate-200/50 rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>

            <div class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Bank Sampah Digital RW. Hak Cipta Dilindungi.
            </div>
        </div>
    </body>
</html>
