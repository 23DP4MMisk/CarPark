<div class="min-h-screen bg-gray-50 py-8">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ url()->previous() ?: '/' }}"
               class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Atpakaļ
            </a>
        </div>

        @php
            $carImage = $car->images->first()?->url ?? null;
        @endphp

        <div class="grid gap-6 lg:grid-cols-[1.1fr_1.9fr]">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="h-56 bg-gradient-to-br from-slate-100 to-gray-200">
                    @if ($carImage)
                        <img src="{{ $carImage }}" alt="{{ $car->brand }} {{ $car->model }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 18l2.586-2.586a2 2 0 012.828 0L22 18v2H4v-4zm4-9a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                {{ $car->brand }} {{ $car->model }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $car->class }} · {{ $car->seats }} vietas · {{ $car->transmission }} · {{ $car->fuel_type }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600" data-test="car-address">
                                {{ $car->address ?: 'Adrese nav norādīta' }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-emerald-50 px-3 py-2 text-right">
                            <div class="text-xs uppercase tracking-wide text-emerald-700">Cena</div>
                            <div class="text-lg font-semibold text-emerald-900">
                                €{{ number_format((float) $car->price_per_day, 2) }}
                            </div>
                            <div class="text-xs text-emerald-700">dienā</div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-lg bg-gray-50 p-3 text-sm text-gray-600">
                        <div class="font-medium text-gray-800">Atcelšanas noteikumi</div>
                        <p class="mt-1">Rezervāciju iespējams atcelt līdz 24 stundām pirms sākuma laika.</p>
                    </div>

                    @if ($car->lat !== null && $car->lng !== null)
                        <div wire:ignore id="car-map" data-test="car-map"
                             class="mt-4 h-40 w-full overflow-hidden rounded-lg shadow-sm"
                             x-data
                             x-init="
                                 const m = L.map($el, { scrollWheelZoom: false }).setView([{{ (float) $car->lat }}, {{ (float) $car->lng }}], 15);
                                 L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(m);
                                 L.marker([{{ (float) $car->lat }}, {{ (float) $car->lng }}]).addTo(m);
                             "></div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="mb-5 rounded-md border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-800" data-test="notice-12h">
                    Automašīnu var rezervēt vismaz 12 stundas pirms nomas sākuma.
                </div>

                <form wire:submit="save" class="space-y-5" data-test="booking-form">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">
                                Nomas sākums
                            </label>
                            <input
                                id="start_time"
                                type="datetime-local"
                                wire:model.live="start_time"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            >
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600" data-test="error-start">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">
                                Nomas beigas
                            </label>
                            <input
                                id="end_time"
                                type="datetime-local"
                                wire:model.live="end_time"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            >
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600" data-test="error-end">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <fieldset class="rounded-lg border border-gray-200 p-4">
                        <legend class="px-1 text-sm font-medium text-gray-700">Papildu pakalpojumi</legend>
                        <div class="mt-2 space-y-2">
                            @foreach ($extraOptions as $key => $extra)
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" value="{{ $key }}" wire:model.live="extras" class="rounded border-gray-300 text-emerald-900">
                                    {{ $extra['label'] }} — €{{ $extra['price'] }}{{ $extra['per_day'] ? ' / dienā' : ' (vienreizēji)' }}
                                </label>
                            @endforeach
                        </div>
                        @error('extras.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 p-4">
                        <h2 class="text-sm font-semibold text-gray-800">Kopējā cena</h2>

                        @if ($prices)
                            <dl class="mt-3 space-y-2 text-sm text-gray-700" data-test="price-breakdown">
                                <div class="flex items-center justify-between">
                                    <dt>Noma ({{ $prices['days'] }} d.)</dt>
                                    <dd>€{{ number_format($prices['base'], 2) }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt>Papildu pakalpojumi</dt>
                                    <dd>€{{ number_format($prices['extras'], 2) }}</dd>
                                </div>
                                <div class="mt-3 flex items-center justify-between border-t border-gray-200 pt-3 text-base font-semibold text-gray-900">
                                    <dt>Kopā</dt>
                                    <dd data-test="total-price">€{{ number_format($prices['total'], 2) }}</dd>
                                </div>
                            </dl>
                        @else
                            <p class="mt-3 text-sm text-gray-400">Izvēlieties laiku, lai redzētu cenu.</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-2">
                        <div class="text-sm text-gray-500">
                            Atcelšana iespējama 24h pirms sākuma.
                        </div>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="inline-flex items-center justify-center rounded-md bg-emerald-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="save">Rezervēt</span>
                            <span wire:loading wire:target="save">Saglabā...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
