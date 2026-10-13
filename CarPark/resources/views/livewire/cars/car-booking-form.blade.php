<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $car->brand }} {{ $car->model }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $car->class }} · {{ $car->seats }} vietas · {{ $car->transmission }} · {{ $car->fuel_type }}</p>
        <p class="mt-1 text-sm font-medium text-gray-700">€{{ number_format((float) $car->price_per_day, 2) }} / dienā</p>

        <form wire:submit="save" class="mt-4 rounded-lg bg-white p-6 shadow" data-test="booking-form">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Nomas sākums</label>
                    <input id="start_time" type="datetime-local" wire:model.live="start_time" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('start_time') <p class="mt-1 text-xs text-red-600" data-test="error-start">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">Nomas beigas</label>
                    <input id="end_time" type="datetime-local" wire:model.live="end_time" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('end_time') <p class="mt-1 text-xs text-red-600" data-test="error-end">{{ $message }}</p> @enderror
                </div>
            </div>

            <fieldset class="mt-4">
                <legend class="text-sm font-medium text-gray-700">Papildu pakalpojumi</legend>
                <div class="mt-2 space-y-2">
                    @foreach ($extraOptions as $key => $extra)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" value="{{ $key }}" wire:model.live="extras" class="rounded border-gray-300 text-emerald-900">
                            {{ $extra['label'] }} — €{{ $extra['price'] }}{{ $extra['per_day'] ? ' / dienā' : ' (vienreizēji)' }}
                        </label>
                    @endforeach
                </div>
                @error('extras.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </fieldset>

            <div class="mt-4 border-t border-gray-100 pt-4">
                @if ($prices)
                    <dl class="space-y-1 text-sm text-gray-600" data-test="price-breakdown">
                        <div class="flex justify-between"><dt>Noma ({{ $prices['days'] }} d.)</dt><dd>€{{ number_format($prices['base'], 2) }}</dd></div>
                        <div class="flex justify-between"><dt>Papildu pakalpojumi</dt><dd>€{{ number_format($prices['extras'], 2) }}</dd></div>
                        <div class="flex justify-between text-base font-semibold text-gray-900"><dt>Kopā</dt><dd data-test="total-price">€{{ number_format($prices['total'], 2) }}</dd></div>
                    </dl>
                @else
                    <p class="text-sm text-gray-400">Izvēlieties laiku, lai redzētu cenu.</p>
                @endif

                <div class="mt-4 text-right">
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="rounded-md bg-emerald-900 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800 disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">Rezervēt</span>
                        <span wire:loading wire:target="save">Saglabā...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
