<?php

namespace Tests\Feature;

use App\Livewire\Admin\ParkingManager;
use App\Models\ParkingSpot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ParkingManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    private function fillSpotForm($test)
    {
        return $test
            ->set('name', 'Centrs')
            ->set('address', 'Brīvības iela 10, Rīga')
            ->set('lat', '56.9496487')
            ->set('lng', '24.1051865')
            ->set('price_per_hour', '2.50')
            ->set('capacity', 3);
    }

    public function test_create_modal_opens_and_closes_with_cancel(): void
    {
        Livewire::test(ParkingManager::class)
            ->assertDontSeeHtml('data-test="parking-modal"')
            ->call('create')
            ->assertSet('showForm', true)
            ->assertSeeHtml('data-test="parking-modal"')
            ->call('cancel')
            ->assertSet('showForm', false)
            ->assertDontSeeHtml('data-test="parking-modal"');
    }

    public function test_create_modal_closes_after_successful_save(): void
    {
        $test = Livewire::test(ParkingManager::class)->call('create');

        $this->fillSpotForm($test)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showForm', false)
            ->assertDontSeeHtml('data-test="parking-modal"')
            ->assertSeeHtml('data-test="flash-success"');

        $this->assertDatabaseCount('parking_spots', 1);
    }

    public function test_edit_modal_opens_with_spot_data(): void
    {
        $spot = ParkingSpot::create([
            'name' => 'Ziemeļi',
            'address' => 'Ziemeļu iela 1, Rīga',
            'lat' => 56.97,
            'lng' => 24.12,
            'price_per_hour' => 3,
            'capacity' => 2,
            'is_active' => true,
        ]);

        Livewire::test(ParkingManager::class)
            ->call('edit', $spot->id)
            ->assertSet('showForm', true)
            ->assertSet('name', 'Ziemeļi')
            ->assertSeeHtml('data-test="parking-modal"');
    }

    public function test_escape_key_handler_is_wired_to_cancel(): void
    {
        Livewire::test(ParkingManager::class)
            ->call('create')
            ->assertSeeHtml('wire:keydown.escape.window="cancel"');
    }
}
