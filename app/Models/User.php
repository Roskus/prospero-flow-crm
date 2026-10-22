<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OAT;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

#[OAT\Schema(
    schema: 'User',
    required: ['first_name', 'email', 'password'],
    properties: [
        new OAT\Property(property: 'id', description: 'User ID', type: 'integer', example: 1),
        new OAT\Property(property: 'first_name', description: 'First name of the user', type: 'string', example: 'John'),
        new OAT\Property(property: 'last_name', description: 'Last name of the user', type: 'string', example: 'Smith'),
        new OAT\Property(property: 'email', description: 'Email of the user', type: 'string', format: 'email', example: 'john.smith@company.com'),
        new OAT\Property(property: 'phone', description: 'Phone of the user', type: 'string', example: '+3464500000'),
        new OAT\Property(property: 'photo', description: 'Profile photo filename', type: 'string', example: 'profile.jpg'),
        new OAT\Property(property: 'lang', description: 'Language ISO code', type: 'string', example: 'es'),
        new OAT\Property(property: 'must_change_password', description: 'Whether the user must change their password on next login', type: 'boolean', example: false),
        new OAT\Property(property: 'active', description: 'Inactive users cannot log in', type: 'boolean', example: true),
        new OAT\Property(property: 'timezone', description: 'Timezone of the user', type: 'string', example: 'UTC'),
        new OAT\Property(property: 'last_login_at', description: 'Last login date and time', type: 'string', format: 'date-time'),
        new OAT\Property(property: 'last_login_ip', description: 'Last login IP address', type: 'string'),
    ],
    type: 'object'
)]
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'user';

    /**
     * Roles and permissions are seeded for the web guard only; pin it so API (JWT) requests resolve them too.
     */
    protected $guard_name = 'web';

    protected $fillable = [
        'company_id',
        'default_branch_id',
        'manager_id',
        'first_name',
        'last_name',
        'email', 'phone',
        'password',
        'photo',
        'lang',
        'timezone',
        'must_change_password',
        'employee_number',
        'is_employee',
        'hire_date',
        'vacation_days_override',
        'weekly_hours_override',
        'iban',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'company_id',
        'password',
        'remember_token',
        'deleted_at',
    ];

    /**
     * Same default as the database column, so new instances are active before being refreshed.
     */
    protected $attributes = [
        'active' => true,
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'is_employee' => 'boolean',
        'must_change_password' => 'boolean',
        'active' => 'boolean',
        'hire_date' => 'date:Y-m-d',
    ];

    protected $with = ['company'];

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if ($user->default_branch_id === null) {
                $user->default_branch_id = Branch::ofCompany($user->defaultCompanyId())->where('is_main', true)->value('id');
            }
        });
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return HasOne<Company, $this>
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    /**
     * Own company of a user with multi-company access; null for everyone else.
     */
    public function defaultCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'default_company_id');
    }

    /**
     * Branch of the user's default company the user works from; kept when the branch is deleted or the user switches company.
     */
    public function defaultBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'default_branch_id');
    }

    /**
     * The default branch when it is an active branch of the active company, otherwise the active company's main branch.
     */
    public function currentBranch(): ?Branch
    {
        return once(function (): ?Branch {
            $branch = $this->default_branch_id !== null
                ? Branch::ofCompany((int) $this->company_id)->find($this->default_branch_id)
                : null;

            return $branch ?? Branch::ofCompany((int) $this->company_id)->where('is_main', true)->first();
        });
    }

    /**
     * Companies the user can access besides their default company.
     */
    public function extraCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_company', 'user_id', 'company_id')
            ->withPivot('granted_by')
            ->withTimestamps();
    }

    /**
     * The company the user belongs to: stored for multi-company users, otherwise the current one.
     */
    public function defaultCompanyId(): int
    {
        return (int) ($this->default_company_id ?? $this->company_id);
    }

    public function hasMultiCompanyAccess(): bool
    {
        return $this->default_company_id !== null;
    }

    /**
     * The default company is answered in memory, so single company users never query the pivot.
     */
    public function canAccessCompany(int $companyId): bool
    {
        if ($companyId === $this->defaultCompanyId()) {
            return true;
        }

        if (! $this->hasMultiCompanyAccess()) {
            return false;
        }

        return $this->extraCompanies()->whereKey($companyId)->exists();
    }

    public function canCreateCompany(): bool
    {
        if ($this->hasRole('SuperAdmin')) {
            return true;
        }

        if (! $this->hasRole(self::MULTI_COMPANY_ADMIN)) {
            return false;
        }

        return (Company::query()->find($this->defaultCompanyId())?->availableClientQuota() ?? 0) > 0;
    }

    /**
     * Outside SuperAdmin only the companies the user can access can be edited.
     */
    public function canUpdateCompany(int $companyId): bool
    {
        if (! $this->can('update company')) {
            return false;
        }

        return $this->hasRole('SuperAdmin') || $this->canAccessCompany($companyId);
    }

    /**
     * Users with access to more than one company.
     */
    #[Scope]
    protected function multiCompany(Builder $query): void
    {
        $query->whereNotNull('default_company_id');
    }

    /**
     * Users an administrator can find to manage: everyone for SuperAdmin, otherwise the users of the
     * administrator's active company and the administrator themselves.
     */
    #[Scope]
    protected function manageableBy(Builder $query, User $administrator): void
    {
        if ($administrator->hasRole('SuperAdmin')) {
            return;
        }

        $query->where(fn (Builder $query) => $query->belongingTo((int) $administrator->company_id)->orWhereKey($administrator->id));
    }

    /**
     * Users whose own (default) company is the given one, wherever they are working right now.
     */
    #[Scope]
    protected function belongingTo(Builder $query, int $companyId): void
    {
        $query->whereRaw('COALESCE(default_company_id, company_id) = ?', [$companyId]);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function getAll()
    {
        return User::orderBy('company_id')->paginate(10);
    }

    public function getFullName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * A "name" property must exist in the model in order for the Mail class
     * to correctly display the name in addresses.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['first_name'].' '.$attributes['last_name'],
        );
    }

    /**
     * A CompanyAdmin that also accesses and creates client companies; it inherits every CompanyAdmin permission.
     */
    public const string MULTI_COMPANY_ADMIN = 'MultiCompanyAdmin';

    /**
     * Roles that administrate a company.
     */
    public const array COMPANY_ADMIN_ROLES = ['CompanyAdmin', self::MULTI_COMPANY_ADMIN];

    /**
     * Administrator roles: SuperAdmin and MultiCompanyAdmin are only assigned and managed by a SuperAdmin;
     * CompanyAdmin also by another company admin of the same company. Other roles (e.g. HR) can neither
     * assign nor manage them.
     */
    public const array ADMINISTRATOR_ROLES = ['SuperAdmin', ...self::COMPANY_ADMIN_ROLES];

    public function isAdministrator(): bool
    {
        return $this->hasAnyRole(self::ADMINISTRATOR_ROLES);
    }

    public function isCompanyAdmin(): bool
    {
        return $this->hasAnyRole(self::COMPANY_ADMIN_ROLES);
    }

    /**
     * SuperAdmin manages everyone and every user manages themselves. A company admin manages anyone of its
     * company but SuperAdmins and MultiCompanyAdmins; any other role only the non-administrators of its company.
     */
    public function canManageUser(User $user): bool
    {
        if ($this->hasRole('SuperAdmin') || $user->is($this)) {
            return true;
        }

        if ($user->defaultCompanyId() !== (int) $this->company_id || $user->hasAnyRole(['SuperAdmin', self::MULTI_COMPANY_ADMIN])) {
            return false;
        }

        return $this->isCompanyAdmin() || ! $user->isAdministrator();
    }

    /**
     * Users change their own data without touching their roles; only SuperAdmin changes their own roles.
     */
    public function canChangeRolesOf(User $user): bool
    {
        return $this->hasRole('SuperAdmin') || ! $user->is($this);
    }

    /**
     * Roles the editor cannot assign are kept as they are, so editing a user never removes them.
     *
     * @param  array<int, string>  $roles
     * @return array<int, string>
     */
    public function rolesToSync(User $user, array $roles): array
    {
        $notAssignableRoles = $user->getRoleNames()->diff($this->assignableRoles()->pluck('name'));

        return array_values(array_unique([...$roles, ...$notAssignableRoles->all()]));
    }

    /**
     * @return Collection<int, Role>
     */
    public function assignableRoles(): Collection
    {
        $forbiddenRoles = match (true) {
            $this->hasRole('SuperAdmin') => [],
            $this->isCompanyAdmin() => ['SuperAdmin', self::MULTI_COMPANY_ADMIN],
            default => self::ADMINISTRATOR_ROLES,
        };

        return Role::query()
            ->whereNotIn('name', $forbiddenRoles)
            ->orderBy('name')
            ->get();
    }

    /**
     * IBANs are payroll data: only users who manage payroll may see other users' bank details.
     */
    public function canSeeBankDetails(): bool
    {
        return $this->can('read payroll');
    }

    public function getAllActiveByCompany(int $company_id)
    {
        return User::where('company_id', $company_id)->get();
    }

    protected function timezone(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? config('app.timezone'),
        );
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
