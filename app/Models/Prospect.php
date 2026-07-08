<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\AssignedSellerScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Squire\Models\Country;
use Yajra\Auditable\AuditableWithDeletesTrait;

abstract class Prospect extends Model
{
    use AuditableWithDeletesTrait;
    use HasFactory;
    use SoftDeletes;

    const string OPEN = 'open';

    const string IN_PROGRESS = 'in_progress';

    const string WAITING_FEEDBACK = 'waiting_feedback';

    const string CONVERTED = 'converted';

    const string CLOSED = 'closed';

    protected const array SORTABLE_COLUMNS = [
        'id', 'name', 'email', 'phone', 'mobile', 'country_id', 'seller_id',
        'status', 'created_at', 'updated_at', 'vat', 'business_name',
    ];

    protected $fillable = [
        'company_id',
        'external_id',
        'name',
        'business_name',
        'dob',
        'vat',
        'phone',
        'phone_verified',
        'extension',
        'phone2',
        'phone2_verified',
        'mobile',
        'mobile_verified',
        'email',
        'email_verified',
        'email2',
        'website',
        'website_verified',
        'source_id',
        'linkedin',
        'facebook',
        'instagram',
        'twitter',
        'youtube',
        'tiktok',
        'notes',
        'seller_id',
        'country_id',
        'province',
        'city',
        'locality',
        'street',
        'address_extra',
        'zipcode',
        'schedule_contact',
        'industry_id',
        'latitude',
        'longitude',
        'opt_in',
        'tags',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'schedule_contact' => 'datetime:Y-m-d H:i',
        'tags' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'dob' => 'date:Y-m-d',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $with = ['country', 'seller', 'industry', 'company'];

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function seller(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function source(): HasOne
    {
        return $this->hasOne(Source::class, 'id', 'source_id');
    }

    public function industry(): HasOne
    {
        return $this->hasOne(Industry::class, 'id', 'industry_id');
    }

    public function getAll(): Collection
    {
        return static::all();
    }

    public function getCountByCompany(int $company_id): int
    {
        return static::where('company_id', $company_id)->count();
    }

    public function getLatestByCompany(int $company_id, int $limit = 10)
    {
        return static::where('company_id', $company_id)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function getStatus(): array
    {
        return [
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In progress',
            self::WAITING_FEEDBACK => 'Waiting for feedback',
            self::CONVERTED => 'Converted',
            self::CLOSED => 'Closed',
        ];
    }

    abstract public function messages();

    abstract public function contacts();

    abstract public function getAllByCompanyId(int $company_id, ?string $search, ?array $filters, ?string $order_by): mixed;

    protected static function booted(): void
    {
        static::addGlobalScope(new AssignedSellerScope);
    }
}
