<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Stāvvietu pārvaldība</h1>
                <p class="mt-1 text-sm text-gray-500">Pievienojiet, rediģējiet un dzēsiet stāvvietas.</p>
            </div>
            <button type="button" wire:click="create" class="rounded-md bg-emerald-900 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800">
                + Pievienot stāvvietu
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
                        <th class="px-4 py-3">Nosaukums</th>
                        <th class="px-4 py-3">Adrese</th>
                        <th class="px-4 py-3">Koordinātes</th>
                        <th class="px-4 py-3">Cena / h</th>
                        <th class="px-4 py-3">Ietilpība</th>
                        <th class="px-4 py-3">Statuss</th>
                        <th class="px-4 py-3 text-right">Darbības</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($spots as $spot)
                        <tr wire:key="spot-{{ $spot->id }}">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $spot->name }}</td>
                            <td class="px-4 py-3">{{ $spot->address }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ number_format((float) $spot->lat, 7) }}, {{ number_format((float) $spot->lng, 7) }}</td>
                            <td class="px-4 py-3">€{{ number_format((float) $spot->price_per_hour, 2) }}</td>
                            <td class="px-4 py-3">{{ $spot->capacity }}</td>
                            <td class="px-4 py-3">
                                <button type="button" wire:click="toggleActive({{ $spot->id }})"
                                    class="rounded-full px-2 py-0.5 text-xs {{ $spot->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $spot->is_active ? 'Aktīva' : 'Neaktīva' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button" wire:click="edit({{ $spot->id }})" class="text-gray-600 hover:text-gray-900">Rediģēt</button>
                                <button type="button" wire:click="delete({{ $spot->id }})" wire:confirm="Dzēst šo stāvvietu?" class="ml-3 text-red-600 hover:text-red-800">Dzēst</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Stāvvietu nav.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $spots->links() }}</div>
    </div>

    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 p-4"
            wire:keydown.escape.window="cancel" data-test="parking-modal">
            <form wire:submit="save" class="my-8 w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl" data-test="parking-form">
                <h2 class="text-lg font-medium text-gray-900">{{ $spotId ? 'Rediģēt stāvvietu' : 'Jauna stāvvieta' }}</h2>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nosaukums</label>
                        <input id="name" type="text" wire:model="name" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
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
                    <div>
                        <label for="price_per_hour" class="block text-sm font-medium text-gray-700">Cena stundā (€)</label>
                        <input id="price_per_hour" type="text" inputmode="decimal" wire:model="price_per_hour" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('price_per_hour') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700">Ietilpība</label>
                        <input id="capacity" type="number" min="1" wire:model="capacity" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                        @error('capacity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <label class="mt-4 inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-emerald-900">
                    Aktīva
                </label>

                <div class="mt-6 flex gap-3">
                    <button type="submit" wire:loading.attr="disabled" class="rounded-md bg-emerald-900 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800">Saglabāt</button>
                    <button type="button" wire:click="cancel" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Atcelt</button>
                </div>
            </form>
        </div>
    @endif
</div>
