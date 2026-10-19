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

    public function test_create_modal_opens_and_closes(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        Livewire::test(CarManager::class)
            ->assertDontSeeHtml('data-test="car-modal"')
            ->call('openCreate')
            ->assertSet('showModal', true)
            ->assertSeeHtml('data-test="car-modal"')
            ->call('closeModal')
            ->assertSet('showModal', false)
            ->assertDontSeeHtml('data-test="car-modal"');
    }

    public function test_edit_modal_opens_with_car_data_and_closes_after_save(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $car = Car::create([
            'brand' => 'Skoda',
            'model' => 'Octavia',
            'class' => 'comfort',
            'fuel_type' => 'diesel',
            'transmission' => 'automatic',
            'drivetrain' => 'fwd',
            'engine' => '2.0',
            'seats' => 5,
            'price_per_day' => 50,
            'is_active' => true,
        ]);

        Livewire::test(CarManager::class)
            ->call('openEdit', $car->id)
            ->assertSet('showModal', true)
            ->assertSet('brand', 'Skoda')
            ->assertSeeHtml('data-test="car-modal"')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertDontSeeHtml('data-test="car-modal"');
    }
}
