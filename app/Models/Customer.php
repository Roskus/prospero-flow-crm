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
use OpenApi\Attributes as OAT;
use Squire\Models\Country;
use Yajra\Auditable\AuditableWithDeletesTrait;

#[OAT\Schema(schema: 'Customer', required: ['name', 'seller_id', 'country_id'], type: 'object')]
class Customer extends Model
{
    use AuditableWithDeletesTrait;
    use HasFactory;
    use SoftDeletes;

    const string OPEN = 'open'; // New

    const string IN_PROGRESS = 'in_progress';

    const string WAITING_FEEDBACK = 'waiting_feedback';

    const string CONVERTED = 'converted'; // Promoted to customer

    const string CLOSED = 'closed';

    protected $table = 'customer';

    private const array SORTABLE_COLUMNS = [
        'id', 'name', 'email', 'phone', 'mobile', 'country_id', 'seller_id',
        'status', 'created_at', 'updated_at', 'vat', 'business_name',
    ];

    #[OAT\Property(property: 'name', type: 'string', example: 'My Company')]
    #[OAT\Property(property: 'business_name', type: 'string', example: 'My Company S.A.')]
    #[OAT\Property(property: 'dob', type: 'string', format: 'date', example: '1990-02-20')]
    #[OAT\Property(property: 'vat', type: 'string', example: 'ESX1234567X')]
    #[OAT\Property(property: 'phone', type: 'string', example: '+3464500000')]
    #[OAT\Property(property: 'extension', type: 'string', example: '4004')]
    #[OAT\Property(property: 'phone2', type: 'string', example: '+3464500000')]
    #[OAT\Property(property: 'mobile', type: 'string', example: '+3464500000')]
    #[OAT\Property(property: 'email', type: 'string', format: 'email', example: 'jhon.doe@email.com')]
    #[OAT\Property(property: 'email2', type: 'string', format: 'email', example: 'jhon.doe@email.com')]
    #[OAT\Property(property: 'website', type: 'string', format: 'uri', example: 'https://www.company.com')]
    #[OAT\Property(property: 'source_id', type: 'integer', example: 1)]
    #[OAT\Property(property: 'linkedin', type: 'string', format: 'uri', example: 'https://www.linkedin.com/in/profile')]
    #[OAT\Property(property: 'facebook', type: 'string', format: 'uri', example: 'https://www.facebook.com/mycompany')]
    #[OAT\Property(property: 'instagram', type: 'string', format: 'uri', example: 'https://www.instagram.com/mycompany')]
    #[OAT\Property(property: 'twitter', type: 'string', format: 'uri', example: 'https://www.twitter.com/mycompany')]
    #[OAT\Property(property: 'youtube', type: 'string', format: 'uri', example: 'https://www.youtube.com/@mycompany')]
    #[OAT\Property(property: 'tiktok', type: 'string', format: 'uri', example: 'https://www.tiktok.com/@mycompany')]
    #[OAT\Property(property: 'notes', type: 'string', example: 'Some notes about the customer')]
    #[OAT\Property(property: 'seller_id', type: 'integer', example: 1)]
    #[OAT\Property(property: 'country_id', type: 'string', example: 'ES')]
    #[OAT\Property(property: 'province', type: 'string', example: 'Buenos Aires')]
    #[OAT\Property(property: 'city', type: 'string', example: 'Buenos Aires')]
    #[OAT\Property(property: 'locality', type: 'string', example: 'Palermo')]
    #[OAT\Property(property: 'street', type: 'string', example: 'Av. Santa Fe 1234')]
    #[OAT\Property(property: 'address_extra', type: 'string', example: 'Piso 3, Dpto B')]
    #[OAT\Property(property: 'zipcode', type: 'string', example: '1425')]
    #[OAT\Property(property: 'industry_id', type: 'integer', example: 1)]
    #[OAT\Property(property: 'status', type: 'string', example: 'open')]
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
        return $this->hasOne(Source::class, 'id', 'source_id');
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
        if (is_null($order_by) || ! in_array($order_by, self::SORTABLE_COLUMNS, true)) {
            $order_by = 'created_at';
        }

        $customers = Customer::where('company_id', $company_id);

        // Check if database engine supports fulltext search
        $supportsFulltext = $this->supportsFulltext();

        // Apply appropriate search based on fulltext support
        if ($supportsFulltext && ! empty($search)) {
            $customers = $this->applyFulltextSearch($customers, $search);
        } elseif (! empty($search)) {
            $customers = $this->applyBasicSearch($customers, $search);
        }

        // Apply additional filters
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
