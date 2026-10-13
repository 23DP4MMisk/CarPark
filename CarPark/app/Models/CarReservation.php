<?php

namespace App\Models;

use App\Traits\HandlesReservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarReservation extends Model
{
    use HandlesReservation;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'base_price' => 'decimal:2',
            'extras_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'extras' => 'array',
        ];
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
