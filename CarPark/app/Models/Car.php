<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
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

    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model}";
    }
}
