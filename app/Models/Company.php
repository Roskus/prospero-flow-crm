<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasAddress;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;
use Squire\Models\Country;

#[OA\Schema(
    schema: 'Company',
    required: ['name', 'country_id'],
    properties: [
        new OA\Property(property: 'name', description: 'Name of the company', type: 'string', example: 'My Company'),
        new OA\Property(property: 'business_name', description: 'Business name of the company', type: 'string', example: 'My Company S.L.'),
        new OA\Property(property: 'vat', description: 'VAT / TAX Identification Number of the company', type: 'string', example: 'B12345678'),
        new OA\Property(property: 'phone', description: 'Phone of the company', type: 'string', example: '+34645600000'),
        new OA\Property(property: 'email', description: 'Email of the company', type: 'string', format: 'email', example: 'info@company.com'),
        new OA\Property(property: 'country_id', description: 'Country ISO code of the company', type: 'string', example: 'ES'),
        new OA\Property(property: 'province', description: 'Province of the company', type: 'string', example: 'Barcelona'),
        new OA\Property(property: 'city', description: 'City of the company', type: 'string', example: 'Barcelona'),
        new OA\Property(property: 'locality', description: 'Locality of the company', type: 'string', example: 'Castelldefels'),
        new OA\Property(property: 'street', description: 'Street of the company', type: 'string', example: 'Av. Constitución 123'),
        new OA\Property(property: 'address_extra', description: 'Floor, office or suite', type: 'string', example: 'Piso 2'),
        new OA\Property(property: 'zipcode', description: 'Zipcode of the company', type: 'string', example: '08860'),
        new OA\Property(property: 'latitude', description: 'Latitude of the company', type: 'number', format: 'float', example: 41.2792),
        new OA\Property(property: 'longitude', description: 'Longitude of the company', type: 'number', format: 'float', example: 1.9766),
        new OA\Property(property: 'currency', description: 'Default currency of the company', type: 'string', example: 'USD'),
        new OA\Property(property: 'website', description: 'Website of the company', type: 'string', example: 'https://www.company.com'),
    ],
    type: 'object'
)]
class Company extends Model
{
    use HasAddress;
    use HasFactory;
    use SoftDeletes;

    const int ACTIVE = 1;

    const int INACTIVE = 0;

    const int DEFAULT_COMPANY = 1;

    const string CALENDAR_VIEW_MONTH = 'month';

    const string CALENDAR_VIEW_WEEK = 'week';

    protected $table = 'company';

    protected $fillable = [
        'name',
        'business_name',
        'vat',
        'logo',
        'signature_html',
        'phone',
        'email',
        'country_id',
        'province',
        'city',
        'locality',
        'street',
        'address_extra',
        'zipcode',
        'latitude',
        'longitude',
        'currency',
        'website',
        'updated_at',
        'status',
        'last_order_number',
        'inactivity_lock_time',
        'vacation_days_per_year',
        'weekly_hours_full_time',
        'personal_days_per_year',
        'dashboard_calendar_view',
        'managed_companies_limit',
        'parent_company_id',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $with = ['country'];

    /**
     * @param  'active'|'deleted'|'all'|null  $filter  Active companies by default
     * @param  User|null  $user  Limits the list to the companies the user can access, unless SuperAdmin
     */
    public function getAllPaginated(int $limit = 50, ?string $filter = null, ?User $user = null)
    {
        return Company::query()
            ->with(['parent' => fn ($query) => $query->withTrashed()->select(['id', 'name'])])
            ->withCount(['clientCompanies' => fn ($query) => $query->withTrashed()])
            ->when($user !== null && ! $user->hasRole('SuperAdmin'), fn ($query) => $query->where(
                fn ($query) => $query->whereKey($user->defaultCompanyId())
                    ->orWhereHas('grantedUsers', fn ($query) => $query->whereKey($user->id))
            ))
            ->when($filter === 'deleted', fn ($query) => $query->onlyTrashed())
            ->when($filter === 'all', fn ($query) => $query->withTrashed())
            ->orderBy('name', 'asc')
            ->paginate($limit);
    }

    /**
     * Companies that can be the parent of a client company: only one level of clients is allowed.
     *
     * @return Collection<int, Company>
     */
    public function getParentCandidates(?int $exceptCompanyId = null)
    {
        return Company::query()
            ->whereNull('parent_company_id')
            ->when($exceptCompanyId, fn ($query) => $query->whereKeyNot($exceptCompanyId))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getAll()
    {
        return Company::orderBy('name', 'asc')->get();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Users granted access to this company besides its own users.
     */
    public function grantedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_company', 'company_id', 'user_id')
            ->withPivot('granted_by')
            ->withTimestamps();
    }

    /**
     * Parent company that created this client company.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'parent_company_id');
    }

    public function clientCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'parent_company_id');
    }

    /**
     * Deleted client companies still count, so the quota cannot be bypassed by deleting and recreating.
     */
    public function usedClientQuota(): int
    {
        return $this->clientCompanies()->withTrashed()->count();
    }

    /**
     * Most companies have no quota (0), which is answered without counting client companies. Client companies
     * never have quota left, since there is only one level of client companies.
     */
    public function availableClientQuota(): int
    {
        $managedCompaniesLimit = (int) $this->managed_companies_limit;

        if ($managedCompaniesLimit === 0 || $this->parent_company_id !== null) {
            return 0;
        }

        return max(0, $managedCompaniesLimit - $this->usedClientQuota());
    }

    public function currencyRates(): HasMany
    {
        return $this->hasMany(CompanyCurrencyRate::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function getStatusLabel(): string
    {
        return match ((int) $this->getAttribute('status')) {
            self::ACTIVE => __('Active'),
            self::INACTIVE => __('Inactive'),
            default => 'Unknown',
        };
    }
}
