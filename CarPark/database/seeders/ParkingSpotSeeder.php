<?php

namespace Database\Seeders;

use App\Models\ParkingSpot;
use Illuminate\Database\Seeder;

class ParkingSpotSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->spots() as $spot) {
            ParkingSpot::create($spot);
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function spots(): array
    {
        return [
            ['name' => 'Centrs', 'address' => 'Brīvības iela 10, Rīga', 'lat' => 56.9496487, 'lng' => 24.1051865, 'price_per_hour' => 2.50, 'capacity' => 120, 'open_24_7' => true, 'has_video' => true, 'accepts_card' => true, 'distance_from_center' => 0],
            ['name' => 'Vecrīga', 'address' => 'Kaļķu iela 5, Rīga', 'lat' => 56.9482, 'lng' => 24.1119, 'price_per_hour' => 3.00, 'capacity' => 40, 'open_24_7' => false, 'has_video' => true, 'accepts_card' => true, 'distance_from_center' => 300],
            ['name' => 'Ziemeļi', 'address' => 'Tallinas iela 80, Rīga', 'lat' => 56.9590, 'lng' => 24.1125, 'price_per_hour' => 1.50, 'capacity' => 150, 'open_24_7' => true, 'has_video' => false, 'accepts_card' => true, 'distance_from_center' => 1500],
            ['name' => 'Dzirnavas', 'address' => 'Dzirnavu iela 60, Rīga', 'lat' => 56.9568, 'lng' => 24.1208, 'price_per_hour' => 2.00, 'capacity' => 60, 'open_24_7' => true, 'has_video' => true, 'accepts_card' => false, 'distance_from_center' => 800],
            ['name' => 'Skanste', 'address' => 'Skanstes iela 50, Rīga', 'lat' => 56.9435, 'lng' => 24.0950, 'price_per_hour' => 1.20, 'capacity' => 80, 'open_24_7' => false, 'has_video' => false, 'accepts_card' => true, 'distance_from_center' => 1200],
            ['name' => 'Pārdaugava', 'address' => 'Daugavgrīvas iela 2, Rīga', 'lat' => 56.9395, 'lng' => 24.0976, 'price_per_hour' => 1.00, 'capacity' => 100, 'open_24_7' => true, 'has_video' => false, 'accepts_card' => false, 'distance_from_center' => 2000],
            ['name' => 'Mežaparks', 'address' => 'Mežaparka iela 1, Rīga', 'lat' => 56.9700, 'lng' => 24.1290, 'price_per_hour' => 1.80, 'capacity' => 50, 'open_24_7' => false, 'has_video' => false, 'accepts_card' => true, 'distance_from_center' => 3000],
            ['name' => 'Kronvalda', 'address' => 'Kronvalda bulvāris 2, Rīga', 'lat' => 56.9535, 'lng' => 24.1240, 'price_per_hour' => 2.20, 'capacity' => 30, 'open_24_7' => true, 'has_video' => true, 'accepts_card' => true, 'distance_from_center' => 600],
            ['name' => 'Stacija', 'address' => 'Stacijas laukums 2, Rīga', 'lat' => 56.9463, 'lng' => 24.1296, 'price_per_hour' => 2.80, 'capacity' => 50, 'open_24_7' => true, 'has_video' => true, 'accepts_card' => true, 'distance_from_center' => 500],
            ['name' => 'Torņakalns', 'address' => 'Torņakalna iela 2, Rīga', 'lat' => 56.9362, 'lng' => 24.1103, 'price_per_hour' => 1.80, 'capacity' => 70, 'open_24_7' => false, 'has_video' => false, 'accepts_card' => true, 'distance_from_center' => 1800],
        ];
    }
}
