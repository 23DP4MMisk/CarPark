<?php

namespace App\Livewire\Admin;

use App\Models\ParkingSpot;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ParkingManager extends Component
{
    use WithPagination;

    private const PER_PAGE = 10;

    public bool $showForm = false;

    public ?int $spotId = null;

    public string $name = '';

    public string $address = '';

    public string $lat = '';

    public string $lng = '';

    public string $price_per_hour = '';

    public int $capacity = 1;

    public bool $is_active = true;

    /** @return array<string, array<int, string>> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'price_per_hour' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10000'],
            'is_active' => ['boolean'],
        ];
    }

    /** @return array<string, string> */
    protected function validationAttributes(): array
    {
        return [
            'name' => 'nosaukums',
            'address' => 'adrese',
            'lat' => 'platums',
            'lng' => 'garums',
            'price_per_hour' => 'cena stundā',
            'capacity' => 'ietilpība',
        ];
    }

    public function create(): void
    {
        $this->authorizeAdmin();
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $this->authorizeAdmin();

        $spot = ParkingSpot::findOrFail($id);

        $this->resetValidation();
        $this->spotId = $spot->id;
        $this->name = $spot->name;
        $this->address = $spot->address;
        $this->lat = number_format((float) $spot->lat, 7, '.', '');
        $this->lng = number_format((float) $spot->lng, 7, '.', '');
        $this->price_per_hour = number_format((float) $spot->price_per_hour, 2, '.', '');
        $this->capacity = (int) $spot->capacity;
        $this->is_active = $spot->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->authorizeAdmin();

        $this->validate();

        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'lat' => number_format((float) $this->lat, 7, '.', ''),
            'lng' => number_format((float) $this->lng, 7, '.', ''),
            'price_per_hour' => number_format((float) $this->price_per_hour, 2, '.', ''),
            'capacity' => $this->capacity,
            'is_active' => $this->is_active,
        ];

        if ($this->spotId) {
            ParkingSpot::findOrFail($this->spotId)->update($data);
            session()->flash('success', 'Stāvvieta atjaunināta.');
        } else {
            ParkingSpot::create($data);
            session()->flash('success', 'Stāvvieta pievienota.');
        }

        $this->cancel();
    }

    public function toggleActive(int $id): void
    {
        $this->authorizeAdmin();

        $spot = ParkingSpot::findOrFail($id);
        $spot->update(['is_active' => ! $spot->is_active]);
    }

    public function delete(int $id): void
    {
        $this->authorizeAdmin();

        $spot = ParkingSpot::findOrFail($id);

        if ($spot->reservations()->whereIn('status', ['reserved', 'active'])->exists()) {
            session()->flash('error', 'Stāvvietu nevar dzēst, jo tai ir aktīvas rezervācijas.');

            return;
        }

        $spot->delete();
        session()->flash('success', 'Stāvvieta dzēsta.');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function render(): View
    {
        return view('livewire.admin.parking-manager', [
            'spots' => ParkingSpot::latest('id')->paginate(self::PER_PAGE),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(['spotId', 'name', 'address', 'lat', 'lng', 'price_per_hour', 'capacity', 'is_active']);
        $this->resetValidation();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
