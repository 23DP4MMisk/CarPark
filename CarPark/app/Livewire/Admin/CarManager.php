<?php

namespace App\Livewire\Admin;

use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CarManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    private const PER_PAGE = 10;

    private const MAX_IMAGES = 10;

    public bool $showModal = false;

    public ?int $carId = null;

    public string $brand = '';

    public string $model = '';

    public string $class = '';

    public string $fuel_type = '';

    public string $transmission = '';

    public string $drivetrain = '';

    public string $engine = '';

    public string $seats = '5';

    public string $price_per_day = '';

    public string $description = '';

    public bool $is_active = true;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $photos = [];

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'class' => ['required', Rule::in(array_keys(Car::CLASSES))],
            'fuel_type' => ['required', Rule::in(array_keys(Car::FUEL_TYPES))],
            'transmission' => ['required', Rule::in(array_keys(Car::TRANSMISSIONS))],
            'drivetrain' => ['required', Rule::in(array_keys(Car::DRIVETRAINS))],
            'engine' => ['required', 'string', 'max:100'],
            'seats' => ['required', 'integer', 'min:1', 'max:20'],
            'price_per_day' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
            'photos' => ['array', 'max:'.self::MAX_IMAGES],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /** @return array<string, string> */
    protected function validationAttributes(): array
    {
        return [
            'brand' => 'marka',
            'model' => 'modelis',
            'class' => 'klase',
            'fuel_type' => 'degviela',
            'transmission' => 'ātrumkārba',
            'drivetrain' => 'piedziņa',
            'engine' => 'dzinējs',
            'seats' => 'sēdvietu skaits',
            'price_per_day' => 'cena dienā',
            'description' => 'apraksts',
            'photos' => 'attēli',
            'photos.*' => 'attēls',
        ];
    }

    public function openCreate(): void
    {
        $this->authorizeAdmin();
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->authorizeAdmin();

        $car = Car::findOrFail($id);

        $this->resetForm();
        $this->carId = $car->id;
        $this->brand = $car->brand;
        $this->model = $car->model;
        $this->class = $car->class;
        $this->fuel_type = $car->fuel_type;
        $this->transmission = $car->transmission;
        $this->drivetrain = $car->drivetrain;
        $this->engine = $car->engine;
        $this->seats = (string) $car->seats;
        $this->price_per_day = number_format((float) $car->price_per_day, 2, '.', '');
        $this->description = (string) $car->description;
        $this->is_active = $car->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorizeAdmin();

        $this->validate();

        $existing = $this->carId ? CarImage::where('car_id', $this->carId)->count() : 0;
        if ($existing + count($this->photos) > self::MAX_IMAGES) {
            $this->addError('photos', 'Maksimālais attēlu skaits: '.self::MAX_IMAGES.'.');

            return;
        }

        $data = [
            'brand' => $this->brand,
            'model' => $this->model,
            'class' => $this->class,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'drivetrain' => $this->drivetrain,
            'engine' => $this->engine,
            'seats' => (int) $this->seats,
            'price_per_day' => number_format((float) $this->price_per_day, 2, '.', ''),
            'description' => $this->description !== '' ? $this->description : null,
            'is_active' => $this->is_active,
        ];

        $updating = $this->carId !== null;

        DB::transaction(function () use ($data) {
            $car = $this->carId ? Car::findOrFail($this->carId) : new Car;
            $car->fill($data)->save();

            $this->storePhotos($car);
        });

        session()->flash('success', $updating ? 'Automašīna atjaunināta.' : 'Automašīna pievienota.');

        $this->closeModal();
    }

    public function removeImage(int $imageId): void
    {
        $this->authorizeAdmin();

        $image = CarImage::where('car_id', $this->carId)->findOrFail($imageId);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        if ($image->is_primary) {
            CarImage::where('car_id', $this->carId)->orderBy('sort_order')->orderBy('id')->first()
                ?->update(['is_primary' => true]);
        }
    }

    public function delete(int $id): void
    {
        $this->authorizeAdmin();

        $car = Car::with('images')->findOrFail($id);

        if ($car->reservations()->whereIn('status', ['reserved', 'active'])->exists()) {
            session()->flash('error', 'Automašīnu nevar dzēst, jo tai ir aktīvas rezervācijas.');

            return;
        }

        $paths = $car->images->pluck('path')->all();

        $car->delete();
        Storage::disk('public')->delete($paths);

        session()->flash('success', 'Automašīna dzēsta.');
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function render(): View
    {
        return view('livewire.admin.car-manager', [
            'cars' => Car::with('images')->latest('id')->paginate(self::PER_PAGE),
            'currentImages' => $this->carId ? CarImage::where('car_id', $this->carId)->orderBy('sort_order')->orderBy('id')->get() : collect(),
        ]);
    }

    private function storePhotos(Car $car): void
    {
        $hasPrimary = CarImage::where('car_id', $car->id)->where('is_primary', true)->exists();
        $order = (int) CarImage::where('car_id', $car->id)->max('sort_order');

        foreach ($this->photos as $photo) {
            $car->images()->create([
                'path' => $photo->store('cars', 'public'),
                'is_primary' => ! $hasPrimary,
                'sort_order' => ++$order,
            ]);
            $hasPrimary = true;
        }
    }

    private function resetForm(): void
    {
        $this->reset(['carId', 'brand', 'model', 'class', 'fuel_type', 'transmission', 'drivetrain', 'engine', 'seats', 'price_per_day', 'description', 'is_active', 'photos']);
        $this->resetValidation();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
