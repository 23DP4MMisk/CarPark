<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    public const CLASSES = ['economy' => 'Ekonomiskā', 'comfort' => 'Komforta', 'suv' => 'SUV', 'premium' => 'Premium'];

    public const FUEL_TYPES = ['petrol' => 'Benzīns', 'diesel' => 'Dīzelis', 'electric' => 'Elektro'];

    public const TRANSMISSIONS = ['manual' => 'Manuālā', 'automatic' => 'Automātiskā'];

    public const DRIVETRAINS = ['fwd' => 'Priekšpiedziņa', 'rwd' => 'Pakaļpiedziņa', 'awd' => '4x4'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'rating' => 'decimal:1',
            'is_active' => 'boolean',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(CarReservation::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model}";
    }
}
