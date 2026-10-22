<?php

namespace Database\Seeders;

use App\Models\ParkingReservation;
use App\Models\ParkingSpot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ParkingReservationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@carpark.test')->firstOrFail();
        $admin = User::where('email', 'admin@carpark.test')->firstOrFail();
        $spots = ParkingSpot::orderBy('id')->get();
        $base = Carbon::now()->startOfHour();

        // [user, spot index, start offset hours, end offset hours, status]
        $plans = [
            [$user, 0, -72, -70, 'completed'],
            [$user, 6, -1, 3, 'active'],
            [$admin, 3, 24, 28, 'reserved'],
            [$admin, 1, -24, -20, 'completed'],
            [$user, 8, 48, 50, 'cancelled'],
        ];

        foreach ($plans as [$customer, $spotIndex, $fromHours, $toHours, $status]) {
            $spot = $spots[$spotIndex];
            $hours = $toHours - $fromHours;

            ParkingReservation::create([
                'user_id' => $customer->id,
                'parking_spot_id' => $spot->id,
                'start_time' => $base->copy()->addHours($fromHours),
                'end_time' => $base->copy()->addHours($toHours),
                'total_price' => (float) $spot->price_per_hour * $hours,
                'status' => $status,
            ]);
        }
    }
}
