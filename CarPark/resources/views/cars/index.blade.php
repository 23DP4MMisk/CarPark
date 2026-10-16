@php
    $cars = $cars ?? collect();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Auto katalogs</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 text-gray-900 antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <header class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Auto katalogs</p>
                        <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Pieejamās automašīnas</h1>
                    </div>

                    <div class="inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 shadow-sm">
                        <span class="mr-2 text-gray-900">{{ count($cars) }}</span>
                        automašīnas
                    </div>
                </header>

                <section class="mb-8 rounded-lg bg-white p-4 shadow sm:p-5">
                    <form method="GET" class="grid grid-cols-1 gap-4 lg:grid-cols-5">
                        <div class="space-y-2">
                            <label for="brand" class="block text-xs font-medium text-gray-600">Zīmols</label>
                            <select id="brand" name="brand" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="">Visi</option>
                                <option value="Toyota">Toyota</option>
                                <option value="BMW">BMW</option>
                                <option value="Mercedes">Mercedes</option>
                                <option value="Audi">Audi</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="model" class="block text-xs font-medium text-gray-600">Modelis</label>
                            <select id="model" name="model" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="">Visi</option>
                                <option value="Corolla">Corolla</option>
                                <option value="3 Series">3 Series</option>
                                <option value="C-Class">C-Class</option>
                                <option value="A4">A4</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="class" class="block text-xs font-medium text-gray-600">Klase</label>
                            <select id="class" name="class" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="">Visas</option>
                                <option value="Sedans">Sedans</option>
                                <option value="SUV">SUV</option>
                                <option value="Hatchback">Hatchback</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="fuel" class="block text-xs font-medium text-gray-600">Degviela</label>
                            <select id="fuel" name="fuel" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="">Visas</option>
                                <option value="Benzīns">Benzīns</option>
                                <option value="Dīzelis">Dīzelis</option>
                                <option value="Hibrīds">Hibrīds</option>
                                <option value="Elektriskais">Elektriskais</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="price" class="block text-xs font-medium text-gray-600">Cena</label>
                            <select id="price" name="price" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="">Jebkura</option>
                                <option value="under_30">Līdz 30 €/diena</option>
                                <option value="30_60">30–60 €/diena</option>
                                <option value="60_100">60–100 €/diena</option>
                                <option value="over_100">Vairāk nekā 100 €/diena</option>
                            </select>
                        </div>
                    </form>
                </section>

                @if(!empty($cars) && count($cars) > 0)
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($cars as $car)
                            <article class="flex flex-col rounded-lg bg-white p-5 shadow transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                                <div class="relative">
                                    @if(!empty($car['image']))
                                        <img
                                            src="{{ $car['image'] }}"
                                            alt="{{ $car['brand'] ?? 'Auto' }} {{ $car['model'] ?? '' }}"
                                            class="h-52 w-full rounded-md object-cover"
                                        >
                                    @else
                                        <div class="flex h-52 w-full items-center justify-center rounded-md bg-gray-100 text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.5A2.5 2.5 0 015.5 6h.7a2.5 2.5 0 012.2 1.3l.6 1.2h6.8l.6-1.2A2.5 2.5 0 0118.8 6h.7A2.5 2.5 0 0122 8.5v7A2.5 2.5 0 0119.5 18H4.5A2.5 2.5 0 012 15.5v-7zm5 2.5h8m-8 4h8"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <span class="absolute right-3 top-3 rounded-full bg-white px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-700 shadow-sm">
                                        {{ $car['class'] ?? 'Standarta' }}
                                    </span>
                                </div>

                                <div class="mt-4 flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-[0.18em] text-gray-500">{{ $car['brand'] ?? 'Zīmols' }}</p>
                                        <h2 class="mt-1 text-lg font-semibold text-gray-900">{{ $car['model'] ?? 'Modelis' }}</h2>
                                    </div>
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                                        {{ $car['fuel'] ?? 'Dīzelis' }}
                                    </span>
                                </div>

                                <p class="mt-2 text-sm text-gray-500">{{ $car['car_class'] ?? 'Premium' }} · {{ $car['fuel'] ?? 'Benzīns' }}</p>

                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-lg font-semibold text-gray-900">
                                        €{{ number_format((float)($car['price_per_day'] ?? 0), 2) }}
                                        <span class="text-sm font-normal text-gray-500">/ dienā</span>
                                    </span>
                                    <button type="button" class="rounded-md bg-emerald-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                                        Rezervēt
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-6 rounded-lg bg-white p-8 text-center shadow">
                        <p class="text-gray-500">Pēc izvēlētajiem filtriem automašīnu nav.</p>
                        <a href="{{ url()->current() }}" class="mt-4 inline-block text-sm font-medium text-emerald-900 hover:underline">
                            Notīrīt filtrus
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </body>
</html>
