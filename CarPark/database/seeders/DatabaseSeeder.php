<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CarSeeder::class,
            CarImageSeeder::class,
            ParkingSpotSeeder::class,
            CarReservationSeeder::class,
            ParkingReservationSeeder::class,
            PaymentSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
