<?php

namespace Tests\Feature;

use App\Livewire\MyBookings;
use App\Models\Car;
use App\Models\CarReservation;
use App\Models\ParkingReservation;
use App\Models\ParkingSpot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyBookingsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private ?Car $car = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    private function carReservation(array $attributes = []): CarReservation
    {
        $this->car ??= Car::create([
            'brand' => 'VW',
            'model' => 'Golf',
            'class' => 'comfort',
            'fuel_type' => 'petrol',
            'transmission' => 'manual',
            'drivetrain' => 'fwd',
            'engine' => '1.5',
            'seats' => 5,
            'price_per_day' => 40,
            'is_active' => true,
        ]);

        return CarReservation::create(array_merge([
            'user_id' => $this->user->id,
            'car_id' => $this->car->id,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(2),
            'base_price' => 80,
            'extras_price' => 0,
            'total_price' => 80,
            'status' => 'reserved',
        ], $attributes));
    }

    private function parkingReservation(): ParkingReservation
    {
        $spot = ParkingSpot::create([
            'name' => 'Centrs',
            'address' => 'Brīvības iela 10, Rīga',
            'lat' => 56.95,
            'lng' => 24.11,
            'price_per_hour' => 2.50,
            'is_active' => true,
        ]);

        return ParkingReservation::create([
            'user_id' => $this->user->id,
            'parking_spot_id' => $spot->id,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(2),
            'total_price' => 5,
            'status' => 'reserved',
        ]);
    }

    public function test_car_tab_is_shown_by_default(): void
    {
        $this->carReservation();

        Livewire::test(MyBookings::class)
            ->assertSet('tab', 'car')
            ->assertSee('VW Golf');
    }

    public function test_tabs_switch_between_car_and_parking_reservations(): void
    {
        $this->carReservation();
        $this->parkingReservation();

        Livewire::test(MyBookings::class)
            ->assertSee('VW Golf')
            ->assertDontSee('Centrs')
            ->call('setTab', 'parking')
            ->assertSet('tab', 'parking')
            ->assertSee('Centrs')
            ->assertDontSee('VW Golf')
            ->call('setTab', 'car')
            ->assertSet('tab', 'car')
            ->assertSee('VW Golf')
            ->assertDontSee('Centrs');
    }

    public function test_unknown_tab_falls_back_to_car(): void
    {
        Livewire::test(MyBookings::class)
            ->call('setTab', 'bogus')
            ->assertSet('tab', 'car');
    }

    public function test_status_badges_use_expected_colours(): void
    {
        $this->carReservation(['status' => 'reserved']);
        $this->carReservation(['status' => 'active']);
        $this->carReservation(['status' => 'completed']);
        $this->carReservation(['status' => 'cancelled']);

        Livewire::test(MyBookings::class)
            ->assertSeeHtml('<span class="rounded-full px-2 py-0.5 text-xs bg-blue-100 text-blue-800">Rezervēta</span>')
            ->assertSeeHtml('<span class="rounded-full px-2 py-0.5 text-xs bg-green-100 text-green-800">Aktīva</span>')
            ->assertSeeHtml('<span class="rounded-full px-2 py-0.5 text-xs bg-gray-100 text-gray-700">Pabeigta</span>')
            ->assertSeeHtml('<span class="rounded-full px-2 py-0.5 text-xs bg-red-100 text-red-800">Atcelta</span>');
    }

    public function test_empty_state_is_shown_per_tab(): void
    {
        Livewire::test(MyBookings::class)
            ->assertSee('Automašīnu rezervāciju nav.')
            ->call('setTab', 'parking')
            ->assertSee('Stāvvietu rezervāciju nav.');
    }

    public function test_cancel_button_is_shown_only_when_cancellable(): void
    {
        $this->carReservation(['start_time' => now()->addHours(2), 'end_time' => now()->addHours(4)]);

        Livewire::test(MyBookings::class)
            ->assertDontSeeHtml('wire:click="cancelCar(');
    }

    public function test_cancelling_a_reservation_shows_success_flash(): void
    {
        $reservation = $this->carReservation();

        Livewire::test(MyBookings::class)
            ->assertSeeHtml('wire:click="cancelCar('.$reservation->id.')')
            ->call('cancelCar', $reservation->id)
            ->assertSeeHtml('data-test="flash-success"')
            ->assertSee("Rezervācija #{$reservation->id} atcelta.");

        $this->assertSame('cancelled', $reservation->fresh()->status);
    }

    public function test_late_cancellation_is_rejected_with_error_flash(): void
    {
        $reservation = $this->carReservation(['start_time' => now()->addHours(2), 'end_time' => now()->addHours(4)]);

        Livewire::test(MyBookings::class)
            ->call('cancelCar', $reservation->id)
            ->assertSeeHtml('data-test="flash-error"')
            ->assertSee('Rezervāciju var atcelt vismaz 24 stundas pirms sākuma.');

        $this->assertSame('reserved', $reservation->fresh()->status);
    }
}
