<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Automašīnas</h1>
        <p class="mt-1 text-sm text-gray-500">Izvēlieties automašīnu nomai.</p>

        <div class="mt-6 grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow sm:grid-cols-2 lg:grid-cols-6" data-test="filters">
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-medium text-gray-600">Meklēt</label>
                <input id="search" type="text" wire:model.live.debounce.300ms="search" placeholder="Marka vai modelis" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
            </div>
            <div>
                <label for="class" class="block text-xs font-medium text-gray-600">Klase</label>
                <select id="class" wire:model.live="class" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                    <option value="">Visas</option>
                    @foreach ($classes as $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="fuel" class="block text-xs font-medium text-gray-600">Degviela</label>
                <select id="fuel" wire:model.live="fuel" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                    <option value="">Visas</option>
                    @foreach ($fuels as $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="minPrice" class="block text-xs font-medium text-gray-600">Cena no (€)</label>
                <input id="minPrice" type="number" min="0" step="1" wire:model.live.debounce.500ms="minPrice" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
            </div>
            <div>
                <label for="maxPrice" class="block text-xs font-medium text-gray-600">Cena līdz (€)</label>
                <input id="maxPrice" type="number" min="0" step="1" wire:model.live.debounce.500ms="maxPrice" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
            </div>
        </div>

        <div class="mt-2 flex items-center justify-between text-sm text-gray-500">
            <span wire:loading.remove>Atrastas: {{ $cars->total() }}</span>
            <span wire:loading>Ielādē...</span>
            <button type="button" wire:click="resetFilters" class="text-emerald-900 hover:underline">Notīrīt filtrus</button>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($cars as $car)
                <div wire:key="car-{{ $car->id }}" class="flex flex-col rounded-lg bg-white p-5 shadow" data-test="car-card">
                    <div class="flex items-start justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $car->brand }} {{ $car->model }}</h2>
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ $car->class }}</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">{{ $car->seats }} vietas · {{ $car->transmission }} · {{ $car->fuel_type }}</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-lg font-semibold text-gray-900">€{{ number_format((float) $car->price_per_day, 2) }}<span class="text-sm font-normal text-gray-500"> / dienā</span></span>
                        <a href="{{ route('cars.book', $car) }}" class="rounded-md bg-emerald-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-800">Rezervēt</a>
                    </div>
                </div>
            @empty
                <p class="col-span-full py-8 text-center text-gray-500" data-test="empty">Pēc izvēlētajiem filtriem automašīnu nav.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $cars->links() }}</div>
    </div>
</div>
