<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Stāvvietas</h1>
        <p class="mt-1 text-sm text-gray-500">Aktīvās stāvvietas kartē un sarakstā.</p>

        <div wire:ignore id="parking-map" data-test="parking-map" class="mt-6 h-96 w-full overflow-hidden rounded-lg shadow"
            x-data
            x-init="
                const riga = [56.9496, 24.1052];
                const spots = @js($mapSpots);
                const map = L.map($el).setView(riga, 13);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);

                if (spots.length) {
                    const points = [riga];
                    spots.forEach(spot => {
                        const latlng = [spot.lat, spot.lng];
                        points.push(latlng);

                        const title = document.createElement('strong');
                        title.textContent = spot.name;

                        const address = document.createElement('p');
                        address.className = 'text-sm text-gray-600';
                        address.textContent = spot.address;

                        const price = document.createElement('p');
                        price.className = 'text-sm font-medium text-gray-900';
                        price.textContent = '€' + spot.price + ' / stundā';

                        const link = document.createElement('a');
                        link.href = spot.url;
                        link.textContent = 'Rezervēt';
                        link.className = 'mt-2 inline-block rounded-md bg-emerald-900 px-3 py-1 text-sm font-medium text-white hover:bg-emerald-800';

                        const content = document.createElement('div');
                        content.append(title, address, price, link);

                        L.marker(latlng).addTo(map).bindPopup(content);
                    });
                    map.fitBounds(points, { padding: [30, 30], maxZoom: 15 });
                }
            "></div>

        <ul class="mt-6 divide-y divide-gray-100 rounded-lg bg-white shadow" data-test="parking-list">
            @forelse ($spots as $spot)
                <li class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:justify-between" wire:key="spot-{{ $spot->id }}">
                    <div>
                        <p class="font-medium text-gray-900">{{ $spot->name }}</p>
                        <p class="text-sm text-gray-500">{{ $spot->address }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-700">€{{ number_format((float) $spot->price_per_hour, 2) }} / stundā</span>
                        <a href="{{ route('parking.book', $spot) }}" class="rounded-md bg-emerald-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-800">Rezervēt</a>
                    </div>
                </li>
            @empty
                <li class="px-4 py-6 text-center text-sm text-gray-500">Stāvvietu nav.</li>
            @endforelse
        </ul>
    </div>
</div>
