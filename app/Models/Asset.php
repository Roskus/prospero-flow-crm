<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AcquisitionType;
use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use App\Enums\AssetType;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OAT;

/**
 * @property int $id
 * @property int $company_id
 * @property AssetCategory $category
 * @property AssetType $type
 * @property AssetStatus $status
 * @property string $code
 * @property string $name
 * @property ?string $brand
 * @property ?string $model
 * @property ?string $serial_number
 * @property ?int $branch_id
 * @property ?int $supplier_id
 * @property AcquisitionType $acquisition_type
 * @property ?Carbon $purchase_date
 * @property ?string $purchase_price
 * @property ?string $rental_monthly_fee
 * @property ?Carbon $rental_starts_at
 * @property ?Carbon $rental_ends_at
 * @property ?string $currency
 * @property ?Carbon $warranty_until
 * @property ?Carbon $next_maintenance_at
 * @property ?string $notes
 * @property ?string $photo_path
 * @property ?Carbon $updated_at
 * @property ?AssetVehicle $vehicle
 * @property ?AssetDevice $device
 * @property ?AssetAssignment $currentAssignment
 */
#[OAT\Schema(
    schema: 'Asset',
    required: ['category', 'type', 'code', 'name'],
    properties: [
        new OAT\Property(property: 'id', description: 'Asset ID', type: 'integer', example: 1),
        new OAT\Property(property: 'category', description: 'Asset category', type: 'string', enum: ['vehicle', 'device'], example: 'vehicle'),
        new OAT\Property(property: 'type', description: 'Asset type, valid for its category', type: 'string', example: 'crane'),
        new OAT\Property(property: 'status', description: 'Asset status', type: 'string', enum: ['available', 'assigned', 'in_maintenance', 'retired'], example: 'available'),
        new OAT\Property(property: 'code', description: 'Internal code, unique in the company', type: 'string', example: 'GR-001'),
        new OAT\Property(property: 'name', description: 'Name of the asset', type: 'string', example: 'Grúa torre 1'),
        new OAT\Property(property: 'brand', description: 'Brand', type: 'string', example: 'Liebherr'),
        new OAT\Property(property: 'model', description: 'Model', type: 'string', example: '132 EC-H8'),
        new OAT\Property(property: 'serial_number', description: 'Serial number', type: 'string', example: 'SN-123456'),
        new OAT\Property(property: 'acquisition_type', description: 'How the company holds the asset', type: 'string', enum: ['purchase', 'renting'], example: 'purchase'),
        new OAT\Property(property: 'purchase_date', description: 'Purchase date', type: 'string', format: 'date', example: '2024-05-10'),
        new OAT\Property(property: 'purchase_price', description: 'Purchase price', type: 'number', format: 'float', example: 125000.00),
        new OAT\Property(property: 'rental_monthly_fee', description: 'Monthly renting fee', type: 'number', format: 'float', example: 450.00),
        new OAT\Property(property: 'rental_starts_at', description: 'Renting contract start date', type: 'string', format: 'date', example: '2026-01-01'),
        new OAT\Property(property: 'rental_ends_at', description: 'Renting contract end date', type: 'string', format: 'date', example: '2029-12-31'),
        new OAT\Property(property: 'currency', description: 'Currency of the purchase price or renting fee', type: 'string', example: 'EUR'),
        new OAT\Property(property: 'warranty_until', description: 'Warranty end date', type: 'string', format: 'date', example: '2027-05-10'),
        new OAT\Property(property: 'next_maintenance_at', description: 'Next maintenance due date', type: 'string', format: 'date', example: '2026-12-01'),
    ],
    type: 'object',
)]
class Asset extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Days ahead in which a due date is flagged as expiring soon.
     */
    const int EXPIRY_WARNING_DAYS = 30;

    const string EXPIRED = 'expired';

    const string EXPIRING = 'expiring';

    protected $table = 'asset';

    protected $fillable = [
        'company_id',
        'category',
        'type',
        'status',
        'code',
        'name',
        'brand',
        'model',
        'serial_number',
        'branch_id',
        'supplier_id',
        'acquisition_type',
        'purchase_date',
        'purchase_price',
        'rental_monthly_fee',
        'rental_starts_at',
        'rental_ends_at',
        'currency',
        'warranty_until',
        'notes',
    ];

    protected $hidden = [
        'company_id',
        'photo_path',
        'deleted_at',
    ];

    protected $attributes = [
        'status' => 'available',
        'acquisition_type' => 'purchase',
    ];

    protected function casts(): array
    {
        return [
            'category' => AssetCategory::class,
            'type' => AssetType::class,
            'status' => AssetStatus::class,
            'acquisition_type' => AcquisitionType::class,
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'rental_monthly_fee' => 'decimal:2',
            'rental_starts_at' => 'date',
            'rental_ends_at' => 'date',
            'warranty_until' => 'date',
            'next_maintenance_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    /**
     * @return BelongsTo<Supplier, $this>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    /**
     * @return HasOne<AssetVehicle, $this>
     */
    public function vehicle(): HasOne
    {
        return $this->hasOne(AssetVehicle::class);
    }

    /**
     * @return HasOne<AssetDevice, $this>
     */
    public function device(): HasOne
    {
        return $this->hasOne(AssetDevice::class);
    }

    /**
     * @return HasMany<AssetAssignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    /**
     * The open assignment, there is at most one per asset.
     *
     * @return HasOne<AssetAssignment, $this>
     */
    public function currentAssignment(): HasOne
    {
        return $this->hasOne(AssetAssignment::class)->whereNull('end_date');
    }

    /**
     * @return HasMany<AssetMaintenance, $this>
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function isVehicle(): bool
    {
        return $this->category === AssetCategory::Vehicle;
    }

    public function isRenting(): bool
    {
        return $this->acquisition_type === AcquisitionType::Renting;
    }

    /**
     * Vehicles age from their manufacturing year, since they are often bought used.
     */
    public function agesFromManufacturingYear(): bool
    {
        return $this->isVehicle() && $this->vehicle?->manufacturing_year !== null;
    }

    /**
     * Date the age is counted from: January 1st of the manufacturing year for vehicles that have it,
     * otherwise when the company got the asset (purchase or renting start).
     */
    public function ageStartDate(): ?Carbon
    {
        if ($this->agesFromManufacturingYear()) {
            return Carbon::create($this->vehicle->manufacturing_year);
        }

        return $this->isRenting() ? $this->rental_starts_at : $this->purchase_date;
    }

    /**
     * Age in years and months, e.g. "3 years 2 months"; null when unknown or in the future.
     */
    public function age(): ?string
    {
        $since = $this->ageStartDate();

        if ($since === null || $since->isFuture()) {
            return null;
        }

        $elapsed = $since->diff(today());

        if ($elapsed->y === 0 && $elapsed->m === 0) {
            return __('Less than a month');
        }

        return CarbonInterval::years($elapsed->y)->months($elapsed->m)->forHumans(['parts' => 2]);
    }

    public function hasPhoto(): bool
    {
        return ! empty($this->photo_path);
    }

    /**
     * Due dates that feed the expiry indicators, keyed by their label.
     *
     * @return array<string, Carbon>
     */
    public function dueDates(): array
    {
        $vehicle = $this->isVehicle() ? $this->vehicle : null;

        return array_filter([
            __('Insurance') => $vehicle?->insurance_expires_at,
            __('Technical inspection') => $vehicle?->inspection_expires_at,
            __('Warranty') => $this->warranty_until,
            __('Renting end') => $this->rental_ends_at,
            __('Next maintenance') => $this->next_maintenance_at,
        ]);
    }

    /**
     * The worst state of a due date: expired, expiring or null when it is fine.
     */
    public static function dateState(?Carbon $date): ?string
    {
        if ($date === null) {
            return null;
        }

        if ($date->lt(today())) {
            return self::EXPIRED;
        }

        return $date->lte(today()->addDays(self::EXPIRY_WARNING_DAYS)) ? self::EXPIRING : null;
    }

    /**
     * The worst expiry state among all due dates; retired assets are never flagged.
     */
    public function expiryState(): ?string
    {
        if ($this->status === AssetStatus::Retired) {
            return null;
        }

        $states = array_map(self::dateState(...), $this->dueDates());

        if (in_array(self::EXPIRED, $states, true)) {
            return self::EXPIRED;
        }

        return in_array(self::EXPIRING, $states, true) ? self::EXPIRING : null;
    }

    #[Scope]
    protected function ofCompany(Builder $query, int $companyId): void
    {
        $query->where('company_id', $companyId);
    }

    #[Scope]
    protected function ofCategory(Builder $query, AssetCategory $category): void
    {
        $query->where('category', $category->value);
    }

    /**
     * Assets with any due date already past or within the warning window.
     */
    #[Scope]
    protected function expiringSoon(Builder $query): void
    {
        $limit = today()->addDays(self::EXPIRY_WARNING_DAYS)->toDateString();

        $query->where(function (Builder $query) use ($limit): void {
            $query->where('warranty_until', '<=', $limit)
                ->orWhere('next_maintenance_at', '<=', $limit)
                ->orWhere('rental_ends_at', '<=', $limit)
                ->orWhereHas('vehicle', function (Builder $query) use ($limit): void {
                    $query->where('insurance_expires_at', '<=', $limit)
                        ->orWhere('inspection_expires_at', '<=', $limit);
                });
        });
    }

    #[Scope]
    protected function assignedTo(Builder $query, int $userId): void
    {
        $query->whereHas('currentAssignment', fn (Builder $query) => $query->where('user_id', $userId));
    }
}
