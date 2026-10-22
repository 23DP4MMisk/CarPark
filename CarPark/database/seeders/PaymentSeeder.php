<?php

namespace Database\Seeders;

use App\Models\CarReservation;
use App\Models\ParkingReservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        CarReservation::all()->each(fn (CarReservation $r) => $this->insertFor($r));
        ParkingReservation::all()->each(fn (ParkingReservation $r) => $this->insertFor($r));
    }

    private function insertFor(Model $reservation): void
    {
        $cancelled = $reservation->status === 'cancelled';
        $now = now();

        DB::table('payments')->insert([
            'user_id' => $reservation->user_id,
            'payable_type' => $reservation->getMorphClass(),
            'payable_id' => $reservation->id,
            'amount' => $reservation->total_price,
            'currency' => 'EUR',
            'method' => 'card',
            'status' => $cancelled ? 'refunded' : 'paid',
            'paid_at' => $cancelled ? null : $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
