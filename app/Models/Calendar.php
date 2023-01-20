<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 *  @OA\Schema(
 *    schema="Calendar",
 *    type="object",
 *    required={"user_id", "start_date", "title"},
 *    @OA\Property(
 *        property="user_id",
 *        description="Id of the User",
 *        type="int",
 *        example="1"
 *    ),
 *    @OA\Property(
 *        property="start_date",
 *        description="Start ISO date of the event",
 *        type="string",
 *        format="date",
 *        example="YYYY-MM-DD"
 *    ),
 *    @OA\Property(
 *        property="title",
 *        description="Title of the calendar event",
 *        type="string",
 *        example="My amaizing event!"
 *    ),
 *
 *  )
 */
class Calendar extends Model
{
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
}
