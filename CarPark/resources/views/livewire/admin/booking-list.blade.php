<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Rezervācijas</h1>
        <p class="mt-1 text-sm text-gray-500">Visas automašīnu un stāvvietu rezervācijas.</p>

        @if (session('success'))
            <div class="mt-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800" data-test="flash-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mt-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-800" data-test="flash-error">{{ session('error') }}</div>
        @endif

        <div class="mt-6 flex flex-wrap gap-4">
            <div>
                <label for="type" class="block text-xs font-medium text-gray-600">Tips</label>
                <select id="type" wire:model.live="type" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm">
                    <option value="">Visi</option>
                    <option value="car">Auto</option>
                    <option value="parking">Stāvvieta</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-xs font-medium text-gray-600">Statuss</label>
                <select id="status" wire:model.live="status" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm">
                    <option value="">Visi</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-lg bg-white shadow">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Lietotājs</th>
                        <th class="px-4 py-3">Tips</th>
                        <th class="px-4 py-3">Objekts</th>
                        <th class="px-4 py-3">Datums</th>
                        <th class="px-4 py-3">Summa</th>
                        <th class="px-4 py-3">Statuss</th>
                        <th class="px-4 py-3 text-right">Darbības</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($reservations as $reservation)
                        @php
                            $isCar = $reservation instanceof \App\Models\CarReservation;
                            $type = $isCar ? 'car' : 'parking';
                            $badge = match ($reservation->status) {
                                'active' => 'bg-green-100 text-green-800',
                                'reserved' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr wire:key="{{ $type }}-{{ $reservation->id }}">
                            <td class="px-4 py-3">{{ $reservation->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $isCar ? 'Auto' : 'Stāvvieta' }}</td>
                            <td class="px-4 py-3">
                                {{ $isCar ? $reservation->car?->full_name : $reservation->parkingSpot?->name }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $reservation->start_time->format('Y-m-d H:i') }} – {{ $reservation->end_time->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-4 py-3">€{{ $reservation->total_price }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $badge }}">
                                    {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($reservation->isCancellable())
                                    <button type="button"
                                        wire:click="cancel('{{ $type }}', {{ $reservation->id }})"
                                        wire:confirm="Vai tiešām atcelt šo rezervāciju?"
                                        class="text-sm font-medium text-red-600 hover:text-red-800">
                                        Atcelt
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Rezervācijas nav atrastas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $reservations->links() }}</div>
    </div>
</div>
