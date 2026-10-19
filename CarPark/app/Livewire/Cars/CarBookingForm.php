<?php

namespace App\Livewire\Cars;

use App\Models\Car;
use App\Models\CarReservation;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class CarBookingForm extends Component
{
    /** Papildu pakalpojumi: cena un vai tā tiek rēķināta par katru dienu. */
    public const MIN_HOURS_AHEAD = 12;

    public const EXTRAS = [
        'child_seat' => ['label' => 'Bērnu sēdeklis', 'price' => 5, 'per_day' => true],
        'gps' => ['label' => 'GPS navigācija', 'price' => 5, 'per_day' => true],
        'full_fuel' => ['label' => 'Pilna degviela', 'price' => 10, 'per_day' => false],
    ];

    public Car $car;

    #[Validate('required|date|after:now')]
    public string $start_time = '';

    #[Validate('required|date|after:start_time')]
    public string $end_time = '';

    /** @var array<int, string> */
    public array $extras = [];

    /** @return array<string, array<int, string>> */
    protected function rules(): array
    {
        return [
            'extras' => ['array'],
            'extras.*' => ['in:'.implode(',', array_keys(self::EXTRAS))],
        ];
    }

    public function mount(Car $car): void
    {
        abort_unless($car->is_active, 404);

        $this->car = $car;
    }

    public function save(): void
    {
        $this->validate();

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        if ($start->lt(now()->addHours(self::MIN_HOURS_AHEAD))) {
            $this->addError('start_time', 'Rezervācija iespējama vismaz 12 stundas iepriekš.');

            return;
        }

        if (CarReservation::hasConflict('car_id', $this->car->id, $start, $end)) {
            $this->addError('start_time', 'Šī automašīna jau ir rezervēta izvēlētajā laikā.');

            return;
        }

        $prices = $this->prices($start, $end);

        CarReservation::create([
            'user_id' => auth()->id(),
            'car_id' => $this->car->id,
            'start_time' => $start,
            'end_time' => $end,
            'base_price' => $prices['base'],
            'extras_price' => $prices['extras'],
            'total_price' => $prices['total'],
            'extras' => array_values($this->selectedExtras()),
            'status' => CarReservation::STATUS_RESERVED,
        ]);

        session()->flash('success', 'Automašīna veiksmīgi rezervēta.');

        $this->redirectRoute('my-bookings');
    }

    public function render(): View
    {
        return view('livewire.cars.car-booking-form', [
            'prices' => $this->previewPrices(),
            'extraOptions' => self::EXTRAS,
        ]);
    }

    /** @return array<int, string> */
    private function selectedExtras(): array
    {
        return array_values(array_intersect($this->extras, array_keys(self::EXTRAS)));
    }

    /** @return array{days: int, base: float, extras: float, total: float} */
    private function prices(Carbon $start, Carbon $end): array
    {
        $days = CarReservation::calculateDays($start, $end);
        $base = round($days * (float) $this->car->price_per_day, 2);

        $extras = 0.0;
        foreach ($this->selectedExtras() as $key) {
            $extra = self::EXTRAS[$key];
            $extras += $extra['per_day'] ? $extra['price'] * $days : $extra['price'];
        }

        return ['days' => $days, 'base' => $base, 'extras' => round($extras, 2), 'total' => round($base + $extras, 2)];
    }

    /** @return array{days: int, base: float, extras: float, total: float}|null */
    private function previewPrices(): ?array
    {
        if ($this->start_time === '' || $this->end_time === '') {
            return null;
        }

        try {
            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);
        } catch (\Throwable) {
            return null;
        }

        return $end->gt($start) ? $this->prices($start, $end) : null;
    }
}
