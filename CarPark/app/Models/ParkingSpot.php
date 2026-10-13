<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkingSpot extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'price_per_hour' => 'decimal:2',
            'rating' => 'decimal:1',
            'open_24_7' => 'boolean',
            'has_video' => 'boolean',
            'accepts_card' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(ParkingReservation::class);
    }
}
