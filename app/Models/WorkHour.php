<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkHour extends Model
{
    use HasFactory;

    protected $table = 'work_hour';

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'type',
        'is_manual',
        'notes',
        'ip_address',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_manual' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Entries of the user that overlap the interval; an open entry (no end time) runs from its start onwards.
     * Back-to-back entries (one ends when the next starts) do not overlap, so split shifts are allowed.
     */
    #[Scope]
    protected function overlapping(Builder $query, int $userId, CarbonInterface $start, CarbonInterface $end, ?int $exceptId = null): void
    {
        $query->where('user_id', $userId)
            ->when($exceptId !== null, fn (Builder $query) => $query->whereKeyNot($exceptId))
            ->where('start_time', '<', $end)
            ->where(fn (Builder $query) => $query->whereNull('end_time')->orWhere('end_time', '>', $start));
    }

    /**
     * Message shown when an entry overlaps this one.
     */
    public function overlapMessage(): string
    {
        return __('The time entry overlaps another one of the same person (:date :start — :end).', [
            'date' => $this->start_time->format('d/m/Y'),
            'start' => $this->start_time->format('H:i'),
            'end' => $this->end_time?->format('H:i') ?? '…',
        ]);
    }

    /**
     * Weeks between the current week and the week of the date, as used by the `week` filter of the time entries page.
     */
    public static function weekOffset(CarbonInterface $date): int
    {
        return (int) round(now()->startOfWeek()->diffInWeeks($date->copy()->startOfWeek(), false));
    }
}
