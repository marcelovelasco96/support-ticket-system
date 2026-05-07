<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema de Soporte Interno</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/logo-demo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="mb-6">
            <div class="bg-white rounded-full p-3 shadow-md">
                <img src="{{ asset('images/logo-demo.png') }}" class="w-16 h-16 object-contain">
            </div>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-6 bg-[#00528e] text-white shadow-xl overflow-hidden rounded-xl
            animate-[fadeInUp_.45s_ease-out]">
            {{ $slot }}
        </div>

        <div class="mt-6 text-center text-xs text-slate-500">
            Equipo de soporte<br>
            v{{ config('app.version') }} | © {{ date('Y') }}
        </div>
    </div>
</body>

</html>
