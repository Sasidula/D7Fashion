<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>D7Fashion</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
    <body class="font-sans text-gray-900 antialiased">
        <div
             class="min-h-screen bg-cover bg-center relative overflow-y-auto flex flex-col sm:justify-center items-center py-12"
             style="background-image: url('https://images.unsplash.com/photo-1718184021018-d2158af6b321?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D')"
        >
            <!-- Logo & Title -->
            <div class="flex flex-col items-center justify-center text-center transition-all duration-300 ease-in-out"
                 :class="{ 'opacity-0 scale-95': showForm, 'opacity-100 scale-100': !showForm }">

                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                     class="w-32 h-32 rounded-full shadow-lg mb-4" />

                <h1 class="text-5xl font-bold text-white mt-6">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#fd9c0a] to-[#ff6b00]">D7</span>Fashion
                </h1>

                <p class="text-white text-lg mt-2">
                    Garment Factory Management System
                </p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
