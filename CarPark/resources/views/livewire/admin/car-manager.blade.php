<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Automašīnu pārvaldība</h1>
                <p class="mt-1 text-sm text-gray-500">Pievienojiet, rediģējiet un dzēsiet automašīnas.</p>
            </div>
            <button type="button" wire:click="openCreate" class="rounded-md bg-emerald-900 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800">
                + Pievienot automašīnu
            </button>
        </div>

        @if (session('success'))
            <div class="mt-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800" data-test="flash-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mt-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-800" data-test="flash-error">{{ session('error') }}</div>
        @endif

        <div class="mt-6 overflow-x-auto rounded-lg bg-white shadow">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Attēls</th>
                        <th class="px-4 py-3">Automašīna</th>
                        <th class="px-4 py-3">Klase</th>
                        <th class="px-4 py-3">Degviela</th>
                        <th class="px-4 py-3">Sēdvietas</th>
                        <th class="px-4 py-3">Cena / dienā</th>
                        <th class="px-4 py-3">Statuss</th>
                        <th class="px-4 py-3 text-right">Darbības</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($cars as $car)
                        @php $cover = $car->images->firstWhere('is_primary', true) ?? $car->images->first(); @endphp
                        <tr wire:key="car-{{ $car->id }}">
                            <td class="px-4 py-3">
                                @if ($cover)
                                    <img src="{{ $cover->url }}" alt="{{ $car->full_name }}" class="h-12 w-20 rounded object-cover">
                                @else
                                    <div class="flex h-12 w-20 items-center justify-center rounded bg-gray-100 text-xs text-gray-400">Nav</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $car->full_name }}</td>
                            <td class="px-4 py-3">{{ \App\Models\Car::CLASSES[$car->class] ?? $car->class }}</td>
                            <td class="px-4 py-3">{{ \App\Models\Car::FUEL_TYPES[$car->fuel_type] ?? $car->fuel_type }}</td>
                            <td class="px-4 py-3">{{ $car->seats }}</td>
                            <td class="px-4 py-3">€{{ number_format((float) $car->price_per_day, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs {{ $car->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $car->is_active ? 'Aktīva' : 'Neaktīva' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <button type="button" wire:click="openEdit({{ $car->id }})" class="text-gray-600 hover:text-gray-900">Rediģēt</button>
                                <button type="button" wire:click="delete({{ $car->id }})" wire:confirm="Dzēst šo automašīnu?" class="ml-3 text-red-600 hover:text-red-800">Dzēst</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">Automašīnu nav.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $cars->links() }}</div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 p-4"
            wire:keydown.escape.window="closeModal" data-test="car-modal">
            <form wire:submit="save" class="my-8 w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl">
                <h2 class="text-lg font-medium text-gray-900">{{ $carId ? 'Rediģēt automašīnu' : 'Jauna automašīna' }}</h2>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700">Marka</label>
                        <input id="brand" type="text" wire:model="brand" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('brand') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700">Modelis</label>
                        <input id="model" type="text" wire:model="model" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('model') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="class" class="block text-sm font-medium text-gray-700">Klase</label>
                        <select id="class" wire:model="class" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">Izvēlieties</option>
                            @foreach (\App\Models\Car::CLASSES as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('class') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="fuel_type" class="block text-sm font-medium text-gray-700">Degviela</label>
                        <select id="fuel_type" wire:model="fuel_type" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">Izvēlieties</option>
                            @foreach (\App\Models\Car::FUEL_TYPES as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('fuel_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="transmission" class="block text-sm font-medium text-gray-700">Ātrumkārba</label>
                        <select id="transmission" wire:model="transmission" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">Izvēlieties</option>
                            @foreach (\App\Models\Car::TRANSMISSIONS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('transmission') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="drivetrain" class="block text-sm font-medium text-gray-700">Piedziņa</label>
                        <select id="drivetrain" wire:model="drivetrain" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">Izvēlieties</option>
                            @foreach (\App\Models\Car::DRIVETRAINS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('drivetrain') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="engine" class="block text-sm font-medium text-gray-700">Dzinējs</label>
                        <input id="engine" type="text" wire:model="engine" placeholder="2.0 dīzelis" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('engine') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="seats" class="block text-sm font-medium text-gray-700">Sēdvietas</label>
                        <input id="seats" type="number" min="1" max="20" wire:model="seats" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('seats') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="price_per_day" class="block text-sm font-medium text-gray-700">Cena dienā (€)</label>
                        <input id="price_per_day" type="text" inputmode="decimal" wire:model="price_per_day" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('price_per_day') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Adrese</label>
                        <input id="address" type="text" wire:model="address" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="lat" class="block text-sm font-medium text-gray-700">Platums (lat)</label>
                        <input id="lat" type="text" inputmode="decimal" wire:model="lat" placeholder="56.9496487" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('lat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="lng" class="block text-sm font-medium text-gray-700">Garums (lng)</label>
                        <input id="lng" type="text" inputmode="decimal" wire:model="lng" placeholder="24.1051865" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('lng') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Apraksts</label>
                    <textarea id="description" rows="3" wire:model="description" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm"></textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4">
                    <label for="photos" class="block text-sm font-medium text-gray-700">Attēli (JPG, PNG, WEBP, līdz 2 MB)</label>
                    <input id="photos" type="file" multiple accept="image/jpeg,image/png,image/webp" wire:model="photos" class="mt-1 block w-full text-sm">
                    <div wire:loading wire:target="photos" class="mt-1 text-xs text-gray-500">Augšupielādē...</div>
                    @error('photos') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @error('photos.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    <div class="mt-3 flex flex-wrap gap-3">
                        @foreach ($currentImages as $image)
                            <div class="relative" wire:key="image-{{ $image->id }}">
                                <img src="{{ $image->url }}" alt="" class="h-20 w-28 rounded object-cover {{ $image->is_primary ? 'ring-2 ring-emerald-700' : '' }}">
                                <button type="button" wire:click="removeImage({{ $image->id }})" wire:confirm="Dzēst šo attēlu?"
                                    class="absolute -right-2 -top-2 h-5 w-5 rounded-full bg-red-600 text-xs leading-5 text-white" aria-label="Dzēst attēlu">&times;</button>
                            </div>
                        @endforeach
                        @foreach ($photos as $index => $photo)
                            @if ($photo->isPreviewable() && ! $errors->has("photos.$index"))
                                <img src="{{ $photo->temporaryUrl() }}" alt="" wire:key="new-photo-{{ $index }}" class="h-20 w-28 rounded object-cover opacity-80">
                            @endif
                        @endforeach
                    </div>
                </div>

                <label class="mt-4 inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-emerald-900">
                    Aktīva (redzama katalogā)
                </label>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="closeModal" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Atcelt</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="photos,save" class="rounded-md bg-emerald-900 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800 disabled:opacity-50">Saglabāt</button>
                </div>
            </form>
        </div>
    @endif
</div>
