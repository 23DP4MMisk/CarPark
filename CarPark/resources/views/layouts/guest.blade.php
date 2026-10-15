<!DOCTYPE html>
@php($wide = $wide ?? false)
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CarPark') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-brand-50 px-4 py-10">
            <a href="/" wire:navigate class="mb-8 flex items-center gap-3">
                <x-application-logo class="h-12 w-12 fill-current text-brand-700" />
                <span class="text-xl font-semibold text-brand-900">CarPark</span>
            </a>

            <div class="w-full {{ $wide ? 'max-w-4xl md:grid md:grid-cols-2' : 'max-w-md' }} overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-gray-900/5">
                @if ($wide)
                    <div class="hidden flex-col justify-between bg-gradient-to-br from-brand-900 to-brand-700 p-10 text-white md:flex">
                        <div>
                            <h2 class="text-3xl font-bold">Laipni lūdzam CarPark</h2>
                            <p class="mt-3 text-sm text-brand-100">Automašīnu noma un autostāvvietu rezervācija vienuviet.</p>
                        </div>
                        <p class="text-xs text-brand-200">&copy; {{ date('Y') }} CarPark</p>
                    </div>
                @endif

                <div class="p-8 sm:p-10">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
