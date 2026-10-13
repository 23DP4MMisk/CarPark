<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\BookingList;
use App\Models\Car;
use App\Models\CarReservation;
use App\Models\ParkingReservation;
use App\Models\ParkingSpot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BookingListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function carReservation(string $status = 'reserved'): CarReservation
    {
        $car = Car::create([
            'brand' => 'BMW', 'model' => 'X3', 'class' => 'suv', 'fuel_type' => 'diesel',
            'transmission' => 'automatic', 'drivetrain' => 'awd', 'engine' => '2.0 diesel',
            'seats' => 5, 'price_per_day' => 60,
        ]);

        return CarReservation::create([
            'user_id' => User::factory()->create()->id,
            'car_id' => $car->id,
            'start_time' => now()->addDays(3),
            'end_time' => now()->addDays(5),
            'base_price' => 120, 'total_price' => 120,
            'status' => $status,
        ]);
    }

    private function parkingReservation(string $status = 'reserved'): ParkingReservation
    {
        $spot = ParkingSpot::create([
            'name' => 'Centra stāvvieta', 'address' => 'Brīvības iela 42, Rīga',
            'lat' => 56.95, 'lng' => 24.1, 'price_per_hour' => 2,
        ]);

        return ParkingReservation::create([
            'user_id' => User::factory()->create()->id,
            'parking_spot_id' => $spot->id,
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(2)->addHours(3),
            'total_price' => 6,
            'status' => $status,
        ]);
    }

    public function test_page_is_forbidden_for_non_admins_and_guests(): void
    {
        $this->get('/admin/bookings')->assertRedirect('/login');

        $this->actingAs(User::factory()->create())->get('/admin/bookings')->assertForbidden();
    }

    public function test_lists_car_and_parking_reservations(): void
    {
        $this->carReservation();
        $this->parkingReservation();

        $this->actingAs($this->admin())->get('/admin/bookings')->assertOk();

        Livewire::actingAs($this->admin())
            ->test(BookingList::class)
            ->assertSee('BMW X3')
            ->assertSee('Centra stāvvieta');
    }

    public function test_filters_by_type_and_status(): void
    {
        $this->carReservation('reserved');
        $this->parkingReservation('completed');

        Livewire::actingAs($this->admin())
            ->test(BookingList::class)
            ->set('type', 'car')
            ->assertSee('BMW X3')
            ->assertDontSee('Centra stāvvieta')
            ->set('type', '')
            ->set('status', 'completed')
            ->assertSee('Centra stāvvieta')
            ->assertDontSee('BMW X3');
    }

    public function test_admin_can_cancel_car_and_parking_reservations(): void
    {
        $car = $this->carReservation('reserved');
        $parking = $this->parkingReservation('active');

        Livewire::actingAs($this->admin())
            ->test(BookingList::class)
            ->call('cancel', 'car', $car->id)
            ->call('cancel', 'parking', $parking->id);

        $this->assertSame('cancelled', $car->fresh()->status);
        $this->assertSame('cancelled', $parking->fresh()->status);
    }

    public function test_completed_reservation_cannot_be_cancelled(): void
    {
        $car = $this->carReservation('completed');

        Livewire::actingAs($this->admin())
            ->test(BookingList::class)
            ->call('cancel', 'car', $car->id)
            ->assertSee('vairs nevar atcelt');

        $this->assertSame('completed', $car->fresh()->status);
    }

    public function test_non_admin_cannot_cancel(): void
    {
        $car = $this->carReservation('reserved');

        Livewire::actingAs(User::factory()->create())
            ->test(BookingList::class)
            ->call('cancel', 'car', $car->id)
            ->assertForbidden();

        $this->assertSame('reserved', $car->fresh()->status);
    }
}
