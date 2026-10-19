<?php

namespace Tests\Feature;

use App\Livewire\Admin\CarManager;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CarManagerTest extends TestCase
{
    use RefreshDatabase;

    private function fillCarForm($test)
    {
        return $test
            ->set('brand', 'VW')
            ->set('model', 'Golf')
            ->set('class', 'comfort')
            ->set('fuel_type', 'petrol')
            ->set('transmission', 'manual')
            ->set('drivetrain', 'fwd')
            ->set('engine', '1.5')
            ->set('seats', '5')
            ->set('price_per_day', '40');
    }

    public function test_admin_can_save_car_address_and_coordinates(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $test = Livewire::test(CarManager::class)->call('openCreate');

        $this->fillCarForm($test)
            ->set('address', 'Brīvības iela 1, Rīga')
            ->set('lat', '56.9496487')
            ->set('lng', '24.1051865')
            ->call('save')
            ->assertHasNoErrors();

        $car = Car::firstOrFail();
        $this->assertSame('Brīvības iela 1, Rīga', $car->address);
        $this->assertEqualsWithDelta(56.9496487, (float) $car->lat, 0.0000001);
        $this->assertEqualsWithDelta(24.1051865, (float) $car->lng, 0.0000001);
    }

    public function test_car_is_saved_without_coordinates(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $test = Livewire::test(CarManager::class)->call('openCreate');

        $this->fillCarForm($test)->call('save')->assertHasNoErrors();

        $car = Car::firstOrFail();
        $this->assertNull($car->address);
        $this->assertNull($car->lat);
        $this->assertNull($car->lng);
    }

    public function test_latitude_without_longitude_is_rejected(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $test = Livewire::test(CarManager::class)->call('openCreate');

        $this->fillCarForm($test)
            ->set('lat', '56.9496487')
            ->set('lng', '')
            ->call('save')
            ->assertHasErrors(['lng']);

        $this->assertDatabaseCount('cars', 0);
    }

    public function test_out_of_range_latitude_is_rejected(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $test = Livewire::test(CarManager::class)->call('openCreate');

        $this->fillCarForm($test)
            ->set('lat', '95')
            ->set('lng', '24.1')
            ->call('save')
            ->assertHasErrors(['lat']);

        $this->assertDatabaseCount('cars', 0);
    }
}
