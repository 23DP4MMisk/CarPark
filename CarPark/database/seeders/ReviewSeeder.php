<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarReservation;
use App\Models\ParkingReservation;
use App\Models\ParkingSpot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    private const RATINGS = [5, 4, 5, 3];

    private const COMMENTS = [
        5 => 'Lieliska pieredze, viss kārtībā.',
        4 => 'Labs pakalpojums, ieteiktu.',
        3 => 'Viss normāli, bet varētu būt tīrāks.',
    ];

    public function run(): void
    {
        $i = 0;

        CarReservation::where('status', 'completed')->get()->each(function (CarReservation $r) use (&$i) {
            $this->insert($r->user_id, Car::class, $r->car_id, self::RATINGS[$i++ % count(self::RATINGS)]);
        });

        ParkingReservation::where('status', 'completed')->get()->each(function (ParkingReservation $r) use (&$i) {
            $this->insert($r->user_id, ParkingSpot::class, $r->parking_spot_id, self::RATINGS[$i++ % count(self::RATINGS)]);
        });
    }

    private function insert(int $userId, string $reviewableType, int $reviewableId, int $rating): void
    {
        $now = now();

        DB::table('reviews')->insert([
            'user_id' => $userId,
            'reviewable_type' => $reviewableType,
            'reviewable_id' => $reviewableId,
            'rating' => $rating,
            'comment' => self::COMMENTS[$rating],
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
