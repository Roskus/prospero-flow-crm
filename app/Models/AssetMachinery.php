<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OAT;

/**
 * Machinery detail of a vehicle-category asset (crane, lift, forklift, excavator, generator...):
 * what contractors must keep valid to work on a client's site.
 *
 * @property int $asset_id
 * @property ?string $capacity
 * @property ?string $certification_number
 * @property ?string $certified_by
 * @property ?Carbon $certification_expires_at
 * @property ?Carbon $calibration_expires_at
 * @property ?Carbon $safety_inspection_expires_at
 */
#[OAT\Schema(
    schema: 'AssetMachinery',
    properties: [
        new OAT\Property(property: 'capacity', description: 'Rated capacity with its unit', type: 'string', example: '25 t'),
        new OAT\Property(property: 'certification_number', description: 'Certificate or permit number of the machine', type: 'string', example: 'CERT-2026-0042'),
        new OAT\Property(property: 'certified_by', description: 'Certifying body', type: 'string', example: 'Bureau Veritas'),
        new OAT\Property(property: 'certification_expires_at', description: 'Certification expiry date', type: 'string', format: 'date', example: '2027-03-01'),
        new OAT\Property(property: 'calibration_expires_at', description: 'Calibration expiry date (load limiter, gauges)', type: 'string', format: 'date', example: '2027-01-15'),
        new OAT\Property(property: 'safety_inspection_expires_at', description: 'Periodic safety inspection expiry date', type: 'string', format: 'date', example: '2026-12-31'),
    ],
    type: 'object',
)]
class AssetMachinery extends Model
{
    use HasFactory;

    protected $table = 'asset_machinery';

    protected $primaryKey = 'asset_id';

    public $incrementing = false;

    protected $fillable = [
        'capacity',
        'certification_number',
        'certified_by',
        'certification_expires_at',
        'calibration_expires_at',
        'safety_inspection_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'certification_expires_at' => 'date',
            'calibration_expires_at' => 'date',
            'safety_inspection_expires_at' => 'date',
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
