<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FuelType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OAT;

/**
 * @property int $asset_id
 * @property ?string $license_plate
 * @property ?string $vin
 * @property ?int $manufacturing_year
 * @property ?FuelType $fuel_type
 * @property ?int $odometer_km
 * @property ?int $usage_hours
 * @property ?string $insurance_company
 * @property ?string $insurance_policy
 * @property ?Carbon $insurance_expires_at
 * @property ?Carbon $inspection_expires_at
 */
#[OAT\Schema(
    schema: 'AssetVehicle',
    properties: [
        new OAT\Property(property: 'license_plate', description: 'License plate', type: 'string', example: '1234ABC'),
        new OAT\Property(property: 'vin', description: 'VIN or chassis number', type: 'string', example: 'WVWZZZ1JZXW000001'),
        new OAT\Property(property: 'manufacturing_year', description: 'Manufacturing year', type: 'integer', example: 2022),
        new OAT\Property(property: 'fuel_type', description: 'Fuel type', type: 'string', enum: ['gasoline', 'diesel', 'electric', 'hybrid', 'lpg', 'other'], example: 'diesel'),
        new OAT\Property(property: 'odometer_km', description: 'Odometer in km', type: 'integer', example: 85000),
        new OAT\Property(property: 'usage_hours', description: 'Usage hours, for machinery', type: 'integer', example: 1500),
        new OAT\Property(property: 'insurance_company', description: 'Insurance company', type: 'string', example: 'Mapfre'),
        new OAT\Property(property: 'insurance_policy', description: 'Insurance policy number', type: 'string', example: 'POL-998877'),
        new OAT\Property(property: 'insurance_expires_at', description: 'Insurance expiry date', type: 'string', format: 'date', example: '2027-03-01'),
        new OAT\Property(property: 'inspection_expires_at', description: 'Technical inspection expiry date', type: 'string', format: 'date', example: '2027-06-15'),
    ],
    type: 'object',
)]
class AssetVehicle extends Model
{
    use HasFactory;

    protected $table = 'asset_vehicle';

    protected $primaryKey = 'asset_id';

    public $incrementing = false;

    protected $fillable = [
        'license_plate',
        'vin',
        'manufacturing_year',
        'fuel_type',
        'odometer_km',
        'usage_hours',
        'insurance_company',
        'insurance_policy',
        'insurance_expires_at',
        'inspection_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'fuel_type' => FuelType::class,
            'manufacturing_year' => 'integer',
            'odometer_km' => 'integer',
            'usage_hours' => 'integer',
            'insurance_expires_at' => 'date',
            'inspection_expires_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Asset, $this>
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
