<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HandlesReservation
{
    public const STATUS_RESERVED = 'reserved';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_RESERVED,
        self::STATUS_ACTIVE,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [self::STATUS_RESERVED, self::STATUS_ACTIVE], true);
    }

    public function cancel(): bool
    {
        if (! $this->isCancellable()) {
            return false;
        }

        return $this->forceFill(['status' => self::STATUS_CANCELLED])->save();
    }

    /** Vai intervāls pārklājas ar vismaz $capacity esošām rezervācijām šim objektam. */
    public static function hasConflict(string $foreignKey, int $foreignId, Carbon $start, Carbon $end, int $capacity = 1): bool
    {
        return static::query()
            ->where($foreignKey, $foreignId)
            ->whereIn('status', [self::STATUS_RESERVED, self::STATUS_ACTIVE])
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->count() >= $capacity;
    }
}
