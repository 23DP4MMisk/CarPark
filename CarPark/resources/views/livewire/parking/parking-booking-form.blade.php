<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $spot->name }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $spot->address }}</p>
        <p class="mt-1 text-sm font-medium text-gray-700">€{{ number_format((float) $spot->price_per_hour, 2) }} / stundā</p>

        <div wire:ignore id="spot-map" class="mt-4 h-56 w-full rounded-lg shadow"
            x-data
            x-init="
                const m = L.map($el).setView([{{ (float) $spot->lat }}, {{ (float) $spot->lng }}], 15);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(m);
                L.marker([{{ (float) $spot->lat }}, {{ (float) $spot->lng }}]).addTo(m);
            "></div>

        <div class="mt-4 rounded-md border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-800" data-test="notice-12h">
            Stāvvietu var rezervēt vismaz 12 stundas pirms sākuma.
        </div>

        <form wire:submit="save" class="mt-4 rounded-lg bg-white p-6 shadow" data-test="booking-form">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Sākums</label>
                    <input id="start_time" type="datetime-local" wire:model.live="start_time" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('start_time') <p class="mt-1 text-xs text-red-600" data-test="error-start">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">Beigas</label>
                    <input id="end_time" type="datetime-local" wire:model.live="end_time" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('end_time') <p class="mt-1 text-xs text-red-600" data-test="error-end">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4">
                <div class="text-sm text-gray-600">
                    Kopā:
                    @if ($totalPrice !== null)
                        <span class="text-lg font-semibold text-gray-900" data-test="total-price">€{{ number_format($totalPrice, 2) }}</span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </div>
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="rounded-md bg-emerald-900 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800 disabled:opacity-50">
                    <span wire:loading.remove wire:target="save">Rezervēt</span>
                    <span wire:loading wire:target="save">Saglabā...</span>
                </button>
            </div>
        </form>
    </div>
</div>
