<?php

namespace Tests\Feature;

use App\Livewire\Admin\BookingList;
use App\Models\Car;
use App\Models\CarReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BookingListTest extends TestCase
{
    use RefreshDatabase;

    private function carReservation(): CarReservation
    {
        $customer = User::factory()->create(['name' => 'Jānis Bērziņš']);

        $car = Car::create([
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

        return CarReservation::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(3),
            'base_price' => 80,
            'extras_price' => 0,
            'total_price' => 80,
            'status' => 'reserved',
        ]);
    }

    public function test_details_modal_opens_with_reservation_and_closes(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $reservation = $this->carReservation();

        Livewire::test(BookingList::class)
            ->assertDontSeeHtml('data-test="booking-modal"')
            ->call('openDetails', 'car', $reservation->id)
            ->assertSet('showModal', true)
            ->assertSeeHtml('data-test="booking-modal"')
            ->assertSee('Jānis Bērziņš')
            ->assertSee('VW Golf')
            ->call('closeModal')
            ->assertSet('showModal', false)
            ->assertDontSeeHtml('data-test="booking-modal"');
    }

    public function test_details_modal_rejects_unknown_type(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        Livewire::test(BookingList::class)
            ->call('openDetails', 'bogus', 1)
            ->assertSet('showModal', false)
            ->assertDontSeeHtml('data-test="booking-modal"');
    }

    public function test_details_modal_is_forbidden_for_non_admin(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]));
        $reservation = $this->carReservation();

        Livewire::test(BookingList::class)
            ->call('openDetails', 'car', $reservation->id)
            ->assertSet('showModal', false)
            ->assertDontSeeHtml('data-test="booking-modal"');
    }
}
