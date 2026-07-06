<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Lead\Message;
use App\Models\Scopes\AssignedSellerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;
use Yajra\Auditable\AuditableWithDeletesTrait;

#[OAT\Schema(
    schema: 'Lead',
    required: ['name', 'seller_id'],
    properties: [
        new OAT\Property(property: 'id', description: 'Lead ID', type: 'integer', example: 1),
        new OAT\Property(property: 'name', description: 'Lead name', type: 'string', example: 'My Company'),
        new OAT\Property(property: 'business_name', description: 'Business name', type: 'string', example: 'My Company S.A.'),
        new OAT\Property(property: 'dob', description: 'Date of birth', type: 'string', format: 'date', example: '1990-02-20'),
        new OAT\Property(property: 'vat', description: 'VAT/TAX ID', type: 'string', example: 'ESX1234567X'),
        new OAT\Property(property: 'phone', description: 'Phone number', type: 'string', example: '+3464500000'),
        new OAT\Property(property: 'extension', description: 'Phone extension', type: 'string', example: '4004'),
        new OAT\Property(property: 'phone2', description: 'Alternative phone', type: 'string', example: '+3464500000'),
        new OAT\Property(property: 'mobile', description: 'Mobile number', type: 'string', example: '+3464500000'),
        new OAT\Property(property: 'email', description: 'Email address', type: 'string', format: 'email', example: 'john.doe@email.com'),
        new OAT\Property(property: 'email2', description: 'Alternative email', type: 'string', format: 'email', example: 'john.doe@email.com'),
        new OAT\Property(property: 'website', description: 'Website URL', type: 'string', format: 'uri', example: 'https://www.company.com'),
        new OAT\Property(property: 'source_id', description: 'Source ID', type: 'integer', example: 1),
        new OAT\Property(property: 'linkedin', description: 'LinkedIn profile', type: 'string', format: 'uri', example: 'https://www.linkedin.com/in/profile'),
        new OAT\Property(property: 'facebook', description: 'Facebook profile', type: 'string', format: 'uri', example: 'https://www.facebook.com/mycompany'),
        new OAT\Property(property: 'instagram', description: 'Instagram profile', type: 'string', format: 'uri', example: 'https://www.instagram.com/mycompany'),
        new OAT\Property(property: 'twitter', description: 'Twitter/X profile', type: 'string', format: 'uri', example: 'https://www.twitter.com/mycompany'),
        new OAT\Property(property: 'youtube', description: 'YouTube profile', type: 'string', format: 'uri', example: 'https://www.youtube.com/@mycompany'),
        new OAT\Property(property: 'tiktok', description: 'TikTok profile', type: 'string', format: 'uri', example: 'https://www.tiktok.com/@mycompany'),
        new OAT\Property(property: 'notes', description: 'Notes about the lead', type: 'string', example: 'Some notes about the lead'),
        new OAT\Property(property: 'seller_id', description: 'Seller ID', type: 'integer', example: 1),
        new OAT\Property(property: 'country_id', description: 'Country ISO code', type: 'string', example: 'ES'),
        new OAT\Property(property: 'province', description: 'Province', type: 'string', example: 'Buenos Aires'),
        new OAT\Property(property: 'city', description: 'City', type: 'string', example: 'Buenos Aires'),
        new OAT\Property(property: 'locality', description: 'Locality', type: 'string', example: 'Palermo'),
        new OAT\Property(property: 'street', description: 'Street address', type: 'string', example: 'Av. Santa Fe 1234'),
        new OAT\Property(property: 'address_extra', description: 'Additional address info', type: 'string', example: 'Piso 3, Dpto B'),
        new OAT\Property(property: 'zipcode', description: 'Postal code', type: 'string', example: '1425'),
        new OAT\Property(property: 'industry_id', description: 'Industry ID', type: 'integer', example: 1),
        new OAT\Property(property: 'status', description: 'Lead status', type: 'string', example: 'open'),
    ],
    type: 'object'
)]
class Lead extends Model
{
    use AuditableWithDeletesTrait;
    use HasFactory;
    use SoftDeletes;

    const string OPEN = 'open'; // New

    const string IN_PROGRESS = 'in_progress';

    const string WAITING_FEEDBACK = 'waiting_feedback';

    const string CONVERTED = 'converted'; // Promoted to customer

    const string CLOSED = 'closed';

    protected $table = 'lead';

    private const array SORTABLE_COLUMNS = [
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

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function seller()
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function source(): HasOne
    {
        return $this->hasOne(Source::class, 'id', 'source_id');
    }

    public function industry()
    {
        return $this->hasOne(Industry::class, 'id', 'industry_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'lead_id', 'id');
    }

    public function getAll()
    {
        return Lead::all();
    }

    public function getAllByCompanyId(int $company_id, ?string $search, ?array $filters, ?string $order_by = 'created_at'): mixed
    {
        if (is_null($order_by) || ! in_array($order_by, self::SORTABLE_COLUMNS, true)) {
            $order_by = 'created_at';
        }

        $leads = Lead::where('company_id', $company_id);

        if (! empty($search)) {
            $leads->where(function ($query) use ($search) {
                $words = explode(' ', $search);
                if (count($words) == 1) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('business_name', 'LIKE', "%$search%")
                        ->orWhere('tags', 'LIKE', "%$search%");
                } else {
                    $query->whereFullText(['name', 'business_name'], $search)
                        ->orWhere('tags', 'LIKE', "%$search%");
                }
            });
        }

        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $leads->where($key, $filter);
            }
        }

        return $leads->orderBy($order_by, 'desc')->paginate(10);
    }

    public function getCountByCompany(int $company_id): int
    {
        return Lead::where('company_id', $company_id)->count();
    }

    public function getLatestByCompany(int $company_id, int $limit = 10)
    {
        $leads = Lead::where('company_id', $company_id);
        $leads->orderBy('created_at', 'DESC');

        return $leads->limit($limit)->get();
    }

    public function getScore(): int
    {
        // Ejemplo de lógica de puntuación
        $score = 0;

        // Aumentar puntuación segun criterios específicos
        if (! empty($this->email)) {
            $score = $this->email_verified ? $score + 10 : $score - 10;
        }

        if (! empty($this->mobile)) {
            $score = $this->mobile_verified ? $score + 10 : $score - 10;
        }

        if (! empty($this->phone)) {
            $score = $this->phone_verified ? $score + 10 : $score - 10;
        }

        if ($this->contacts()->count() > 0) {
            $score += 10;
        }

        return $score;
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

    protected static function booted(): void
    {
        static::addGlobalScope(new AssignedSellerScope);
    }
}
