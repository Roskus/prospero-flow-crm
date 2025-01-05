<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Customer\Message;
use App\Models\Scopes\AssignedSellerScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations\OpenApi as OA;
use Squire\Models\Country;
use Yajra\Auditable\AuditableWithDeletesTrait;

/**
 *  @OA\Schema(
 *    schema="Customer",
 *    type="object",
 *    required={"name", "seller_id", "country_id"},
 *    @OA\Property(
 *        property="name",
 *        description="Name of the customer",
 *        type="string",
 *        example="My Company"
 *    ),
 *    @OA\Property(
 *        property="business_name",
 *        description="Business name or legal name of the company",
 *        type="string",
 *        example="My Company S.A."
 *    ),
 *    @OA\Property(
 *        property="dob",
 *        description="Date of fundation of the customer",
 *        type="date",
 *        example="1990-02-20"
 *    ),
 *    @OA\Property(
 *        property="vat",
 *        description="VAT/NIF of the customer",
 *        type="string",
 *        example="ESX1234567X"
 *    ),
 *    @OA\Property(
 *        property="phone",
 *        description="Phone of the customer",
 *        type="string",
 *        example="+3464500000"
 *    ),
 *    @OA\Property(
 *        property="extension",
 *        description="Phone extension of the customer",
 *        type="string",
 *        example="4004"
 *    ),
 *    @OA\Property(
 *        property="phone2",
 *        description="Phone2 of the customer",
 *        type="string",
 *        example="+3464500000"
 *    ),
 *    @OA\Property(
 *        property="mobile",
 *        description="Mobile of the customer",
 *        type="string",
 *        example="+3464500000"
 *    ),
 *    @OA\Property(
 *        property="email",
 *        description="Email of the customer",
 *        type="string",
 *        format="email",
 *        example="jhon.doe@email.com"
 *    ),
 *    @OA\Property(
 *        property="email2",
 *        description="Email2 of the customer",
 *        type="string",
 *        format="email",
 *        example="jhon.doe@email.com"
 *    ),
 *    @OA\Property(
 *        property="website",
 *        description="Website of the customer",
 *        type="string",
 *        format="url",
 *        example="https://www.company.com"
 *    ),
 *    @OA\Property(
 *        property="source_id",
 *        description="Source ID of the lead",
 *        type="int",
 *        example="1"
 *    ),
 *    @OA\Property(
 *        property="linkedin",
 *        description="Linkedin of the customer",
 *        type="string",
 *        format="url",
 *        example="https://www.likedin.com/in/profile"
 *    ),
 *    @OA\Property(
 *         property="notes",
 *         description="Optional Notes",
 *         type="string"
 *    ),
 *    @OA\Property(
 *        property="seller_id",
 *        description="SellerID of the customer",
 *        type="string"
 *    ),
 *    @OA\Property(
 *         property="country_id",
 *         description="ISO country code 2 chars",
 *         type="string"
 *     )
 * )
 */
class Customer extends Model
{
    use AuditableWithDeletesTrait;
    use HasFactory;
    use SoftDeletes;

    const OPEN = 'open'; // New

    const IN_PROGRESS = 'in_progress';

    const WAITING_FEEDBACK = 'waiting_feedback';

    const CONVERTED = 'converted'; // Promoted to customer

    const CLOSED = 'closed';

    protected $table = 'customer';

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
        return $this->hasOne(\App\Models\Company::class, 'id', 'company_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
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
        return $this->hasOne(\App\Models\Source::class, 'id', 'source_id');
    }

    public function industry(): HasOne
    {
        return $this->hasOne(Industry::class, 'id', 'industry_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'customer_id', 'id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'customer_id', 'id');
    }

    public function getAll(): Collection
    {
        return Customer::all();
    }

    public function getAllByCompanyId(
        int $company_id,
        ?string $search = null,
        ?array $filters = null,
        ?string $order_by = 'created_at',
        int $limit = 50): mixed
    {
        if (is_null($order_by)) {
            $order_by = 'created_at';
        }

        $customers = Customer::where('company_id', $company_id);

        // Comprobamos si el motor de base de datos es compatible con búsquedas fulltext
        $supportsFulltext = $this->supportsFulltext();

        // Aplicamos la búsqueda adecuada según el soporte de fulltext
        if ($supportsFulltext && ! empty($search)) {
            $customers = $this->applyFulltextSearch($customers, $search);
        } elseif (! empty($search)) {
            $customers = $this->applyBasicSearch($customers, $search);
        }

        // Apply aditionals filters
        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $customers->where($key, $filter);
            }
        }

        return $customers->orderBy($order_by, 'desc')->paginate($limit);
    }

    protected function isPgTrgmEnabled(): bool
    {
        $connection = DB::connection();
        $result = $connection->select("SELECT * FROM pg_extension WHERE extname = 'pg_trgm'");

        return ! empty($result);
    }

    protected function supportsFulltext(): bool
    {
        $driver = config('database.connections.'.config('database.default').'.driver');

        switch ($driver) {
            case 'mysql':
                // Verificar si estamos utilizando MySQL y si el modo de SQL es 'strict_all_tables'
                return true;
            case 'pgsql':
                // Verificar si estamos utilizando PostgreSQL y si la extensión pg_trgm está habilitada
                return $this->isPgTrgmEnabled();
            default:
                // Otros motores de base de datos no son compatibles con búsquedas fulltext
                return false;
        }
    }

    protected function applyFulltextSearch(Builder $query, string $search): Builder
    {
        // Lógica para aplicar la búsqueda fulltext
        return $query->whereFullText(['name', 'business_name'], $search)
            ->orWhere('tags', 'LIKE', "%$search%");
    }

    protected function applyBasicSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            if (is_numeric($search)) {
                $query->orWhere('external_id', '=', $search)
                    ->orWhere('phone', 'LIKE', "%$search%");
            }

            if (is_string($search)) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('business_name', 'LIKE', "%$search%")
                        ->orWhere('tags', 'LIKE', "%$search%")
                        ->orWhere('external_id', 'LIKE', "%$search%")
                        ->orWhere('vat', 'LIKE', "%$search%");
                });
            }
        });
    }

    public function getCountByCompany(int $company_id): int
    {
        return Customer::where('company_id', $company_id)->count();
    }

    public function getLatestByCompany(int $company_id, int $limit = 10)
    {
        $customers = Customer::where('company_id', $company_id);
        $customers->orderBy('created_at', 'DESC');

        return $customers->limit($limit)->get();
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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new AssignedSellerScope);
    }
}
