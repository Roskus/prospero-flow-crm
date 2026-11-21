<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaintenanceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OAT;

/**
 * @property int $id
 * @property int $asset_id
 * @property MaintenanceType $type
 * @property Carbon $date
 * @property string $description
 * @property ?string $cost
 * @property ?string $currency
 * @property ?int $supplier_id
 * @property ?int $odometer_km
 * @property ?int $usage_hours
 * @property ?Carbon $next_due_at
 * @property ?int $created_by
 * @property ?Asset $asset
 */
#[OAT\Schema(
    schema: 'AssetMaintenance',
    required: ['type', 'date', 'description'],
    properties: [
        new OAT\Property(property: 'id', description: 'Maintenance ID', type: 'integer', example: 1),
        new OAT\Property(property: 'asset_id', description: 'Asset ID', type: 'integer', example: 1),
        new OAT\Property(property: 'type', description: 'Maintenance type', type: 'string', enum: ['preventive', 'corrective', 'inspection'], example: 'preventive'),
        new OAT\Property(property: 'date', description: 'Date of the maintenance', type: 'string', format: 'date', example: '2026-11-20'),
        new OAT\Property(property: 'description', description: 'Work done', type: 'string', example: 'Cambio de aceite y filtros'),
        new OAT\Property(property: 'cost', description: 'Cost', type: 'number', format: 'float', example: 250.00),
        new OAT\Property(property: 'currency', description: 'Currency of the cost', type: 'string', example: 'EUR'),
        new OAT\Property(property: 'odometer_km', description: 'Odometer reading at the maintenance', type: 'integer', example: 85000),
        new OAT\Property(property: 'usage_hours', description: 'Usage hours reading at the maintenance', type: 'integer', example: 1500),
        new OAT\Property(property: 'next_due_at', description: 'Next maintenance due date', type: 'string', format: 'date', example: '2027-05-20'),
    ],
    type: 'object',
)]
class AssetMaintenance extends Model
{
    use HasFactory;

    protected $table = 'asset_maintenance';

    protected $fillable = [
        'asset_id',
        'type',
        'date',
        'description',
        'cost',
        'currency',
        'supplier_id',
        'odometer_km',
        'usage_hours',
        'next_due_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => MaintenanceType::class,
            'date' => 'date',
            'cost' => 'decimal:2',
            'odometer_km' => 'integer',
            'usage_hours' => 'integer',
            'next_due_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Asset, $this>
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * @return BelongsTo<Supplier, $this>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
}
