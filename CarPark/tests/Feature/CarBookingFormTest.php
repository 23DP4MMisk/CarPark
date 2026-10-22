<?php

namespace Tests\Feature;

use App\Livewire\Cars\CarBookingForm;
use App\Models\Car;
use App\Models\CarReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CarBookingFormTest extends TestCase
{
    use RefreshDatabase;

    private function makeCar(): Car
    {
        return Car::create([
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
            'address' => 'Brīvības iela 1, Rīga',
            'lat' => 56.9496,
            'lng' => 24.1052,
        ]);
    }

    public function test_booking_starting_less_than_12_hours_ahead_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        $car = $this->makeCar();

        Livewire::test(CarBookingForm::class, ['car' => $car])
            ->set('start_time', now()->addHours(6)->format('Y-m-d\TH:i'))
            ->set('end_time', now()->addDays(2)->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasErrors(['start_time' => 'Rezervācija iespējama vismaz 12 stundas iepriekš.']);

        $this->assertDatabaseCount('car_reservations', 0);
    }

    public function test_booking_starting_more_than_12_hours_ahead_is_saved(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $car = $this->makeCar();

        Livewire::test(CarBookingForm::class, ['car' => $car])
            ->set('start_time', now()->addHours(13)->format('Y-m-d\TH:i'))
            ->set('end_time', now()->addDays(2)->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('car_reservations', [
            'user_id' => $user->id,
            'car_id' => $car->id,
            'status' => CarReservation::STATUS_RESERVED,
        ]);
    }
}
