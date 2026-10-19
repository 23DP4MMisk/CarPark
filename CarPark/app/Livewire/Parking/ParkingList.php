<?php

namespace App\Livewire\Parking;

use App\Models\ParkingSpot;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ParkingList extends Component
{
    public function render(): View
    {
        $spots = ParkingSpot::where('is_active', true)->orderBy('name')->get();

        return view('livewire.parking.parking-list', [
            'spots' => $spots,
            'mapSpots' => $spots->map(fn (ParkingSpot $spot) => [
                'name' => $spot->name,
                'address' => $spot->address,
                'price' => number_format((float) $spot->price_per_hour, 2),
                'lat' => (float) $spot->lat,
                'lng' => (float) $spot->lng,
                'url' => route('parking.book', $spot),
            ])->values(),
        ]);
    }
}
