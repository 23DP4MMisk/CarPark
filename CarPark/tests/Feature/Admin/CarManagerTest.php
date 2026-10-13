<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\CarManager;
use App\Models\Car;
use App\Models\CarReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CarManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function png(string $name = 'car.png'): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($name, $png);
    }

    private function fill($component)
    {
        return $component
            ->set('brand', 'Toyota')
            ->set('model', 'Corolla')
            ->set('class', 'economy')
            ->set('fuel_type', 'petrol')
            ->set('transmission', 'automatic')
            ->set('drivetrain', 'fwd')
            ->set('engine', '1.8 hybrid')
            ->set('seats', '5')
            ->set('price_per_day', '45.5')
            ->set('description', 'Test');
    }

    private function car(): Car
    {
        return Car::create([
            'brand' => 'BMW', 'model' => 'X3', 'class' => 'suv', 'fuel_type' => 'diesel',
            'transmission' => 'automatic', 'drivetrain' => 'awd', 'engine' => '2.0 diesel',
            'seats' => 5, 'price_per_day' => 60,
        ]);
    }

    public function test_page_is_admin_only(): void
    {
        $this->get('/admin/cars')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/admin/cars')->assertForbidden();
        $this->actingAs($this->admin())->get('/admin/cars')->assertOk();
    }

    public function test_all_fields_are_validated(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CarManager::class)
            ->call('openCreate')
            ->set('seats', '')
            ->call('save')
            ->assertHasErrors(['brand', 'model', 'class', 'fuel_type', 'transmission', 'drivetrain', 'engine', 'seats', 'price_per_day']);

        $this->fill(Livewire::actingAs($this->admin())->test(CarManager::class)->call('openCreate'))
            ->set('class', 'spaceship')
            ->set('price_per_day', '-5')
            ->set('seats', '0')
            ->call('save')
            ->assertHasErrors(['class', 'price_per_day', 'seats']);

        $this->assertSame(0, Car::count());
    }

    public function test_create_car_with_images(): void
    {
        $this->fill(Livewire::actingAs($this->admin())->test(CarManager::class)->call('openCreate'))
            ->set('photos', [$this->png('a.png'), $this->png('b.png')])
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $car = Car::with('images')->firstOrFail();
        $this->assertSame('Toyota', $car->brand);
        $this->assertSame('45.50', $car->price_per_day);
        $this->assertCount(2, $car->images);
        $this->assertTrue($car->images[0]->is_primary);
        $this->assertFalse($car->images[1]->is_primary);

        foreach ($car->images as $image) {
            Storage::disk('public')->assertExists($image->path);
        }
    }

    public function test_non_image_upload_is_rejected(): void
    {
        $this->fill(Livewire::actingAs($this->admin())->test(CarManager::class)->call('openCreate'))
            ->set('photos', [UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')])
            ->call('save')
            ->assertHasErrors('photos.0');

        $this->assertSame(0, Car::count());
    }

    public function test_edit_car_and_add_then_remove_image(): void
    {
        $car = $this->car();

        $component = Livewire::actingAs($this->admin())
            ->test(CarManager::class)
            ->call('openEdit', $car->id)
            ->assertSet('brand', 'BMW')
            ->assertSet('price_per_day', '60.00')
            ->set('brand', 'Audi')
            ->set('photos', [$this->png()])
            ->call('save')
            ->assertHasNoErrors();

        $car->refresh();
        $this->assertSame('Audi', $car->brand);
        $image = $car->images()->firstOrFail();
        Storage::disk('public')->assertExists($image->path);

        $component->call('openEdit', $car->id)->call('removeImage', $image->id);

        Storage::disk('public')->assertMissing($image->path);
        $this->assertSame(0, $car->images()->count());
    }

    public function test_delete_removes_car_and_image_files(): void
    {
        $this->fill(Livewire::actingAs($this->admin())->test(CarManager::class)->call('openCreate'))
            ->set('photos', [$this->png()])
            ->call('save');

        $car = Car::with('images')->firstOrFail();
        $path = $car->images[0]->path;

        Livewire::actingAs($this->admin())->test(CarManager::class)->call('delete', $car->id);

        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
        $this->assertDatabaseCount('car_images', 0);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_car_with_active_reservation_cannot_be_deleted(): void
    {
        $car = $this->car();
        CarReservation::create([
            'user_id' => User::factory()->create()->id, 'car_id' => $car->id,
            'start_time' => now()->addDay(), 'end_time' => now()->addDays(2),
            'base_price' => 60, 'total_price' => 60, 'status' => 'reserved',
        ]);

        Livewire::actingAs($this->admin())
            ->test(CarManager::class)
            ->call('delete', $car->id)
            ->assertSee('aktīvas rezervācijas');

        $this->assertDatabaseHas('cars', ['id' => $car->id]);
    }

    public function test_non_admin_cannot_modify(): void
    {
        $car = $this->car();

        Livewire::actingAs(User::factory()->create())
            ->test(CarManager::class)
            ->call('delete', $car->id)
            ->assertForbidden();

        $this->assertDatabaseHas('cars', ['id' => $car->id]);
    }
}
