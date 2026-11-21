<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;

/**
 * @property int $id
 * @property int $company_id
 * @property string $name
 * @property ?string $country_id
 * @property ?string $phone
 * @property ?string $website
 */
#[OAT\Schema(
    schema: 'Carrier',
    required: ['name'],
    properties: [
        new OAT\Property(property: 'id', description: 'Carrier ID', type: 'integer', example: 1),
        new OAT\Property(property: 'name', description: 'Mobile carrier name, unique in the company', type: 'string', example: 'Movistar'),
        new OAT\Property(property: 'country_id', description: 'ISO 3166-1 alpha-2 country code', type: 'string', example: 'es'),
        new OAT\Property(property: 'phone', description: 'Customer service phone', type: 'string', example: '1004'),
        new OAT\Property(property: 'website', description: 'Website of the carrier', type: 'string', example: 'https://www.movistar.es'),
    ],
    type: 'object',
)]
class Carrier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'carrier';

    protected $fillable = [
        'company_id',
        'name',
        'country_id',
        'phone',
        'website',
    ];

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * @return HasMany<AssetDevice, $this>
     */
    public function devices(): HasMany
    {
        return $this->hasMany(AssetDevice::class);
    }

    #[Scope]
    protected function ofCompany(Builder $query, int $companyId): void
    {
        $query->where('company_id', $companyId);
    }
}
