<?php

namespace App\Livewire;

use App\Models\CarReservation;
use App\Models\ParkingReservation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class MyBookings extends Component
{
    public const CANCEL_LIMIT_HOURS = 24;

    private const TABS = ['car', 'parking'];

    #[Url(except: 'car')]
    public string $tab = 'car';

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, self::TABS, true) ? $tab : 'car';
    }

    public function cancelCar(int $id): void
    {
        $this->cancelReservation(CarReservation::class, $id);
    }

    public function cancelParking(int $id): void
    {
        $this->cancelReservation(ParkingReservation::class, $id);
    }

    public function render(): View
    {
        $userId = auth()->id();

        return view('livewire.my-bookings', [
            'carReservations' => CarReservation::with('car')
                ->where('user_id', $userId)->latest('start_time')->get(),
            'parkingReservations' => ParkingReservation::with('parkingSpot')
                ->where('user_id', $userId)->latest('start_time')->get(),
            'statusLabels' => [
                'reserved' => 'Rezervēta',
                'active' => 'Aktīva',
                'completed' => 'Pabeigta',
                'cancelled' => 'Atcelta',
            ],
            'cancelLimitHours' => self::CANCEL_LIMIT_HOURS,
        ]);
    }

    public static function canCancel(CarReservation|ParkingReservation $reservation): bool
    {
        return $reservation->status === $reservation::STATUS_RESERVED
            && $reservation->start_time->gte(now()->addHours(self::CANCEL_LIMIT_HOURS));
    }

    /** @param  class-string<CarReservation|ParkingReservation>  $model */
    private function cancelReservation(string $model, int $id): void
    {
        $reservation = $model::where('user_id', auth()->id())->findOrFail($id);

        if (! self::canCancel($reservation)) {
            session()->flash('error', 'Rezervāciju var atcelt vismaz '.self::CANCEL_LIMIT_HOURS.' stundas pirms sākuma.');

            return;
        }

        $reservation->cancel();
        session()->flash('success', "Rezervācija #{$reservation->id} atcelta.");
    }
}
