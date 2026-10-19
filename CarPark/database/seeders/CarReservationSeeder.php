<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarReservation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CarReservationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@carpark.test')->firstOrFail();
        $admin = User::where('email', 'admin@carpark.test')->firstOrFail();
        $cars = Car::orderBy('id')->get();
        $base = Carbon::now()->startOfHour();

        // [user, car index, start offset hours, end offset hours, status]
        $plans = [
            [$user, 0, -240, -192, 'completed'],
            [$user, 4, -2, 46, 'active'],
            [$user, 9, 72, 120, 'reserved'],
            [$admin, 2, -72, -48, 'completed'],
            [$user, 7, 168, 192, 'cancelled'],
        ];

        foreach ($plans as [$customer, $carIndex, $fromHours, $toHours, $status]) {
            $car = $cars[$carIndex];
            $days = max(1, (int) ceil(($toHours - $fromHours) / 24));
            $basePrice = (float) $car->price_per_day * $days;

            CarReservation::create([
                'user_id' => $customer->id,
                'car_id' => $car->id,
                'start_time' => $base->copy()->addHours($fromHours),
                'end_time' => $base->copy()->addHours($toHours),
                'base_price' => $basePrice,
                'extras_price' => 0,
                'total_price' => $basePrice,
                'status' => $status,
            ]);
        }
    }
}
