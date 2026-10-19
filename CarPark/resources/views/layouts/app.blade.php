<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="/" class="text-xl font-bold text-indigo-600">CarPark</a>
                <div class="hidden md:flex space-x-4">
                <a href="/" class="text-sm font-medium hover:text-gray-900" :class="{ 'text-indigo-600': window.location.pathname === '/' }">Sākums</a>
                <a href="/parking" class="text-sm font-medium hover:text-gray-900" :class="{ 'text-indigo-600': window.location.pathname.startsWith('/parking') }">Stāvvietas</a>
                @auth
                    <a href="/my-bookings" class="text-sm font-medium hover:text-gray-900" :class="{ 'text-indigo-600': window.location.pathname.startsWith('/my-bookings') }">Manas rezervācijas</a>
                    @if (auth()->user()->is_admin)
                    <a href="/admin/cars" class="text-sm font-medium hover:text-gray-900" :class="{ 'text-indigo-600': window.location.pathname.startsWith('/admin') }">Admin</a>
                    @endif
                @endauth
                </div>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md px-3 py-1.5">Iziet</button>
                </form>
                @endauth
                @guest
                <a href="/login" class="text-sm font-medium text-gray-600 hover:text-gray-900">Ieeja</a>
                <a href="/register" class="text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md px-3 py-1.5">Reģistrēties</a>
                @endguest

                <button @click="open = !open" class="md:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                </button>
            </div>
            </div>

            <div x-show="open" class="md:hidden">
            <div class="space-y-1 px-2 pb-3">
                <a href="/" class="block text-sm font-medium hover:text-gray-900" :class="{ 'text-indigo-600': window.location.pathname === '/' }">Sākums</a>
                <a href="/parking" class="block text-sm font-medium hover:text-gray-900" :class="{ 'text-indigo-600': window.location.pathname.startsWith('/parking') }">Stāvvietas</a>
                @auth
                <a href="/my-bookings" class="block text-sm font-medium hover:text-gray-900" :class="{ 'text-indigo-600': window.location.pathname.startsWith('/my-bookings') }">Manas rezervācijas</a>
                @if (auth()->user()->is_admin)
                    <a href="/admin/cars" class="block text-sm font-medium hover:text-gray-900" :class="{ 'text-indigo-600': window.location.pathname.startsWith('/admin') }">Admin</a>
                @endif
                @endauth
                @guest
                <a href="/login" class="block text-sm font-medium hover:text-gray-900">Ieeja</a>
                <a href="/register" class="block text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md px-3 py-1.5">Reģistrēties</a>
                @endguest
            </div>
            </div>
        </nav>
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    </head>
    <body class="font-sans antialiased flex flex-col min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-8">
                    <a href="/" class="text-xl font-bold text-indigo-600">CarPark</a>
                    <a href="/" class="text-sm font-medium text-gray-600 hover:text-gray-900">Sākums</a>
                    <a href="/parking" class="text-sm font-medium text-gray-600 hover:text-gray-900">Stāvvietas</a>
                    @auth
                        <a href="/my-bookings" class="text-sm font-medium text-gray-600 hover:text-gray-900">Manas rezervācijas</a>
                        @if (auth()->user()->is_admin)
                            <a href="/admin/cars" class="text-sm font-medium text-gray-600 hover:text-gray-900">Admin</a>
                        @endif
                    @endauth
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md px-3 py-1.5">Iziet</button>
                        </form>
                    @endauth
                    @guest
                        <a href="/login" class="text-sm font-medium text-gray-600 hover:text-gray-900">Ieeja</a>
                        <a href="/register" class="text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md px-3 py-1.5">Reģistrēties</a>
                    @endguest
                </div>
            </div>
        </nav>

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="flex-grow">
            {{ $slot }}
        </main>

        <footer class="bg-white border-t border-gray-200 py-4 text-center text-sm text-gray-500">
            &copy; 2025 CarPark
        </footer>

        @livewireScripts
    </body>
</html>
