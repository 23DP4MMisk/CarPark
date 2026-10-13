<?php

namespace App\Livewire\Parking;

use App\Models\ParkingReservation;
use App\Models\ParkingSpot;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class ParkingBookingForm extends Component
{
    public const MIN_HOURS_AHEAD = 12;

    public ParkingSpot $spot;

    #[Validate('required|date|after:now')]
    public string $start_time = '';

    #[Validate('required|date|after:start_time')]
    public string $end_time = '';

    public function mount(ParkingSpot $spot): void
    {
        abort_unless($spot->is_active, 404);

        $this->spot = $spot;
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

        if (ParkingReservation::hasConflict('parking_spot_id', $this->spot->id, $start, $end, max(1, (int) $this->spot->capacity))) {
            $this->addError('start_time', 'Šī vieta jau ir aizņemta.');

            return;
        }

        ParkingReservation::create([
            'user_id' => auth()->id(),
            'parking_spot_id' => $this->spot->id,
            'start_time' => $start,
            'end_time' => $end,
            'total_price' => $this->calculatePrice($start, $end),
            'status' => ParkingReservation::STATUS_RESERVED,
        ]);

        session()->flash('success', 'Stāvvieta veiksmīgi rezervēta.');

        $this->redirectRoute('my-bookings');
    }

    public function render(): View
    {
        return view('livewire.parking.parking-booking-form', [
            'totalPrice' => $this->previewPrice(),
        ]);
    }

    private function calculatePrice(Carbon $start, Carbon $end): float
    {
        $hours = (int) ceil(ceil($start->diffInMinutes($end, true)) / 60);

        return round($hours * (float) $this->spot->price_per_hour, 2);
    }

    private function previewPrice(): ?float
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

        return $end->gt($start) ? $this->calculatePrice($start, $end) : null;
    }
}
