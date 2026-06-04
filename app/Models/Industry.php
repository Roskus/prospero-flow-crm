<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Industry',
    required: ['name'],
    properties: [
        new OAT\Property(property: 'id', type: 'int', example: 6),
        new OAT\Property(property: 'name', type: 'string', example: 'Automotive'),
    ],
    type: 'object'
)]
class Industry extends Model
{
    use HasFactory;

    protected $table = 'industry';

    public function getAll()
    {
        $cacheKey = 'industries_'.app()->getLocale();

        return Cache::remember($cacheKey, 600, function () { // 10 Minutes
            return Industry::whereNull('company_id')->get()
                ->sortBy(fn ($industry) => __('industry.'.$industry->name))
                ->values();
        });
    }

    public function getAllByCompany(int $company_id)
    {
        return Industry::where('company_id', $company_id)->get()
            ->sortBy(fn ($industry) => __('industry.'.$industry->name))
            ->values();
    }
}
