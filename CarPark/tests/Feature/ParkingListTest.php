<?php

namespace Tests\Feature;

use App\Livewire\Parking\ParkingList;
use App\Models\ParkingSpot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ParkingListTest extends TestCase
{
    use RefreshDatabase;

    private function makeSpot(array $attributes = []): ParkingSpot
    {
        return ParkingSpot::create(array_merge([
            'name' => 'Centrs',
            'address' => 'Brīvības iela 10, Rīga',
            'lat' => 56.9500000,
            'lng' => 24.1100000,
            'price_per_hour' => 2.50,
            'is_active' => true,
        ], $attributes));
    }

    public function test_page_lists_only_active_spots_with_booking_links(): void
    {
        $active = $this->makeSpot();
        $inactive = $this->makeSpot(['name' => 'Slēgta', 'is_active' => false]);

        Livewire::test(ParkingList::class)
            ->assertSee('Centrs')
            ->assertSee(route('parking.book', $active))
            ->assertDontSee('Slēgta')
            ->assertDontSee(route('parking.book', $inactive));
    }

    public function test_map_receives_coordinates_address_price_and_url(): void
    {
        $spot = $this->makeSpot();

        Livewire::test(ParkingList::class)
            ->assertViewHas('mapSpots', fn ($mapSpots) => $mapSpots->toArray() === [[
                'name' => 'Centrs',
                'address' => 'Brīvības iela 10, Rīga',
                'price' => '2.50',
                'lat' => 56.95,
                'lng' => 24.11,
                'url' => route('parking.book', $spot),
            ]]);
    }

    public function test_page_shows_empty_state_without_spots(): void
    {
        Livewire::test(ParkingList::class)
            ->assertSee('Stāvvietu nav.')
            ->assertViewHas('mapSpots', fn ($mapSpots) => $mapSpots->isEmpty());
    }
}
