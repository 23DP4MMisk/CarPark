<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Manas rezervācijas</h1>
        <p class="mt-1 text-sm text-gray-500">Rezervāciju var atcelt vismaz {{ $cancelLimitHours }} stundas pirms sākuma.</p>

        @if (session('success'))
            <div class="mt-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800" data-test="flash-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mt-4 rounded-md bg-red-50 px-4 py-3 text-sm text-red-800" data-test="flash-error">{{ session('error') }}</div>
        @endif

        <div class="mt-6 flex gap-2 border-b border-gray-200">
            @foreach (['car' => 'Automašīnas', 'parking' => 'Stāvvietas'] as $key => $label)
                <button type="button" wire:click="setTab('{{ $key }}')" data-test="tab-{{ $key }}"
                    class="-mb-px border-b-2 px-4 py-2 text-sm font-medium {{ $tab === $key ? 'border-emerald-900 text-emerald-900' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @php
            $badges = [
                'reserved' => 'bg-blue-100 text-blue-800',
                'active' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
                'completed' => 'bg-gray-100 text-gray-700',
            ];
        @endphp

        <div class="mt-6 space-y-4">
            @if ($tab === 'car')
                @forelse ($carReservations as $reservation)
                    <div wire:key="car-{{ $reservation->id }}" class="flex items-center justify-between rounded-lg bg-white p-4 shadow">
                        <div>
                            <div class="font-medium text-gray-900">{{ $reservation->car?->brand }} {{ $reservation->car?->model }}</div>
                            <div class="text-sm text-gray-500">{{ $reservation->start_time->format('d.m.Y H:i') }} – {{ $reservation->end_time->format('d.m.Y H:i') }}</div>
                            <div class="mt-1 text-sm text-gray-700">€{{ number_format((float) $reservation->total_price, 2) }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $badges[$reservation->status] ?? $badges['completed'] }}">{{ $statusLabels[$reservation->status] ?? $reservation->status }}</span>
                            @if (\App\Livewire\MyBookings::canCancel($reservation))
                                <button type="button" wire:click="cancelCar({{ $reservation->id }})" wire:confirm="Atcelt šo rezervāciju?" class="text-sm text-red-600 hover:text-red-800">Atcelt</button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">Automašīnu rezervāciju nav.</p>
                @endforelse
            @else
                @forelse ($parkingReservations as $reservation)
                    <div wire:key="parking-{{ $reservation->id }}" class="flex items-center justify-between rounded-lg bg-white p-4 shadow">
                        <div>
                            <div class="font-medium text-gray-900">{{ $reservation->parkingSpot?->name }}</div>
                            <div class="text-sm text-gray-500">{{ $reservation->parkingSpot?->address }}</div>
                            <div class="text-sm text-gray-500">{{ $reservation->start_time->format('d.m.Y H:i') }} – {{ $reservation->end_time->format('d.m.Y H:i') }}</div>
                            <div class="mt-1 text-sm text-gray-700">€{{ number_format((float) $reservation->total_price, 2) }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $badges[$reservation->status] ?? $badges['completed'] }}">{{ $statusLabels[$reservation->status] ?? $reservation->status }}</span>
                            @if (\App\Livewire\MyBookings::canCancel($reservation))
                                <button type="button" wire:click="cancelParking({{ $reservation->id }})" wire:confirm="Atcelt šo rezervāciju?" class="text-sm text-red-600 hover:text-red-800">Atcelt</button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">Stāvvietu rezervāciju nav.</p>
                @endforelse
            @endif
        </div>
    </div>
</div>
