<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\SendCalendarEventService;
use App\Traits\ICalendar;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Calendar', required: ['user_id', 'start_date', 'title'])]
class Calendar extends Model
{
    use HasFactory;
    use ICalendar;

    protected $table = 'calendar';

    protected $fillable = [
        'company_id',
        'user_id',
        'start_date',
        'end_date',
        'is_all_day',
        'start_time',
        'end_time',
        'title',
        'description',
        'guests',
        'meeting',
        'address',
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    protected $casts = [
        'guests' => 'array',
    ];

    #[OAT\Property(property: 'id', type: 'integer', example: 1)]
    private ?int $id; // NOSONAR

    #[OAT\Property(property: 'user_id', type: 'integer', example: 1)]
    protected ?int $user_id;

    #[OAT\Property(property: 'end_date', type: 'string', format: 'date-time', example: '2025-01-15 11:00:00')]
    #[OAT\Property(property: 'description', type: 'string', example: 'Discuss Q1 results')]
    #[OAT\Property(property: 'start_time', type: 'string', example: '10:00')]
    #[OAT\Property(property: 'end_time', type: 'string', example: '11:00')]
    #[OAT\Property(property: 'is_all_day', type: 'boolean', example: false)]
    #[OAT\Property(property: 'meeting', type: 'string', example: 'https://meet.google.com/abc-defg-hij')]
    #[OAT\Property(property: 'address', type: 'string', example: 'Av. Santa Fe 1234, Buenos Aires')]
    #[OAT\Property(property: 'guests', type: 'array', items: new OAT\Items(type: 'string'), example: ['guest@email.com'])]
    public function organizer()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    protected function startDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value, config('app.timezone'))->setTimezone(Auth::user()->timezone)->toDateTimeString(),
            set: fn ($value) => Carbon::parse($value, Auth::user()->timezone)->setTimezone(config('app.timezone'))->toDateTimeString(),
        );
    }

    protected function endDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value, config('app.timezone'))->setTimezone(Auth::user()->timezone)->toDateTimeString(),
            set: fn ($value) => Carbon::parse($value, Auth::user()->timezone)->setTimezone(config('app.timezone'))->toDateTimeString(),
        );
    }

    protected static function booted(): void
    {
        static::created(function (Calendar $calendar) {
            (new SendCalendarEventService)->send($calendar);
        });

        static::updated(function (Calendar $calendar) {
            (new SendCalendarEventService)->send($calendar);
        });
    }
}
