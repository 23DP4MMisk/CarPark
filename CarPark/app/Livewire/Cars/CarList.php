<?php

namespace App\Livewire\Cars;

use App\Models\Car;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CarList extends Component
{
    use WithPagination;

    private const PER_PAGE = 9;

    private const FILTERS = ['search', 'class', 'fuel', 'minPrice', 'maxPrice'];

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $class = '';

    #[Url(except: '')]
    public string $fuel = '';

    #[Url(except: '')]
    public string $minPrice = '';

    #[Url(except: '')]
    public string $maxPrice = '';

    public function updated(string $property): void
    {
        if (in_array($property, self::FILTERS, true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(self::FILTERS);
        $this->resetPage();
    }

    public function render(): View
    {
        $search = trim($this->search);

        $cars = Car::query()
            ->where('is_active', true)
            ->when($search !== '', fn ($q) => $q->where(
                fn ($q) => $q->where('brand', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%"),
            ))
            ->when($this->class !== '', fn ($q) => $q->where('class', $this->class))
            ->when($this->fuel !== '', fn ($q) => $q->where('fuel_type', $this->fuel))
            ->when(is_numeric($this->minPrice), fn ($q) => $q->where('price_per_day', '>=', $this->minPrice))
            ->when(is_numeric($this->maxPrice), fn ($q) => $q->where('price_per_day', '<=', $this->maxPrice))
            ->orderBy('brand')
            ->orderBy('model')
            ->paginate(self::PER_PAGE);

        return view('livewire.cars.car-list', [
            'cars' => $cars,
            'classes' => Car::where('is_active', true)->distinct()->orderBy('class')->pluck('class'),
            'fuels' => Car::where('is_active', true)->distinct()->orderBy('fuel_type')->pluck('fuel_type'),
        ]);
    }
}
