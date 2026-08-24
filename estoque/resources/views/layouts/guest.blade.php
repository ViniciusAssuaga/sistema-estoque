<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta property="og:title" content="Sistema de Estoque">
        <meta property="og:description" content="Sistema de controle de estoque desenvolvido em Laravel.">
        <meta property="og:image" content="{{ asset('tela.png') }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">

        <title>@yield('title', 'Login')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-white antialiased bg-zinc-950">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-zinc-950 px-4">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-[#FF2D20]" />
                </a>
            </div>

            <div class="w-[95%] sm:max-w-md mt-6 px-6 py-6 bg-zinc-900 border border-zinc-800 shadow-2xl overflow-hidden rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
