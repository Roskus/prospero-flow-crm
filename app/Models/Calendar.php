<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Calendar', required: ['user_id', 'start_date', 'title'])]
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

    #[OAT\Property(type: 'int', example: 1)]
    private ?int $id;

    #[OAT\Property(type: 'int', example: 1)]
    private ?int $company_id;

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $user_id;

    #[OAT\Property(type: 'string', format: 'date', example: '2022-01-27')]
    protected ?string $start_date;

    #[OAT\Property(type: 'string', example: 'My awesome event')]
    protected ?string $title;

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
