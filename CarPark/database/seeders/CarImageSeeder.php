<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CarImageSeeder extends Seeder
{
    private const PLACEHOLDER = 'cars/placeholder.svg';

    public function run(): void
    {
        $disk = Storage::disk('public');

        if (! $disk->exists(self::PLACEHOLDER)) {
            $disk->put(self::PLACEHOLDER, <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" width="640" height="400" viewBox="0 0 640 400">
  <rect width="640" height="400" fill="#e5e7eb"/>
  <text x="320" y="210" font-family="sans-serif" font-size="28" text-anchor="middle" fill="#6b7280">Attēls nav pievienots</text>
</svg>
SVG);
        }

        Car::orderBy('id')->get()->each(function (Car $car) {
            $car->images()->create([
                'path' => self::PLACEHOLDER,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        });
    }
}
