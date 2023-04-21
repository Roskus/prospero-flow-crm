<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\AssignedSellerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations\OpenApi as OA;
use Squire\Models\Country;
use Yajra\Auditable\AuditableWithDeletesTrait;

/**
 *  @OA\Schema(
 *    schema="Lead",
 *    type="object",
 *    required={"name", "country"},
 *    @OA\Property(
 *        property="name",
 *        description="Name of the lead",
 *        type="string",
 *        example="My Company"
 *    ),
 *    @OA\Property(
 *        property="business_name",
 *        description="Business name or legal name of the lead",
 *        type="string",
 *        example="My Company S.A."
 *    ),
 *    @OA\Property(
 *        property="dob",
 *        description="Date of fundation of the lead",
 *        type="date",
 *        example="1990-02-20"
 *    ),
 *    @OA\Property(
 *        property="vat",
 *        description="VAT/NIF of the lead",
 *        type="string",
 *        example="ESX1234567X"
 *    ),
 *    @OA\Property(
 *        property="phone",
 *        description="Phone of the lead",
 *        type="string",
 *        example="+3464500000"
 *    ),
 *    @OA\Property(
 *        property="phone2",
 *        description="Phone2 of the lead",
 *        type="string",
 *        example="+3464500000"
 *    ),
 *    @OA\Property(
 *        property="mobile",
 *        description="Mobile of the lead",
 *        type="string",
 *        example="+3464500000"
 *    ),
 *    @OA\Property(
 *        property="email",
 *        description="Email of the lead",
 *        type="string",
 *        format="email",
 *        example="jhon.doe@email.com"
 *    ),
 *    @OA\Property(
 *        property="email2",
 *        description="Email2 of the lead",
 *        type="string",
 *        format="email",
 *        example="jhon.doe@email.com"
 *    ),
 *    @OA\Property(
 *        property="website",
 *        description="Website of the lead",
 *        type="string",
 *        format="url",
 *        example="https://www.company.com"
 *    ),
 *    @OA\Property(
 *        property="linkedin",
 *        description="Linkedin of the lead",
 *        type="string",
 *        format="url",
 *        example="https://www.likedin.com/in/profile"
 *    )
 * )
 */
class Lead extends Model
{
    use HasFactory;
    use SoftDeletes;
    use AuditableWithDeletesTrait;

    const OPEN = 'open'; //New

    const IN_PROGRESS = 'in_progress';

    const CONVERTED = 'converted'; // Promoted to customer

    const CLOSED = 'closed';

    protected $table = 'lead';

    protected $fillable = [
        'external_id',
        'company_id',
        'name',
        'business_name',
        'dob',
        'vat',
        'phone',
        'phone2',
        'mobile',
        'email',
        'email2',
        'website',
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
        return $this->hasOne(\App\Models\Company::class, 'id', 'company_id');
    }

    public function seller()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'seller_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function industry()
    {
        return $this->hasOne(\App\Models\Industry::class, 'id', 'industry_id');
    }

    public function contacts()
    {
        return $this->hasMany(\App\Models\Contact::class, 'lead_id', 'id');
    }

    public function getAll()
    {
        return Lead::all();
    }

    public function getAllByCompanyId(int $company_id, ?string $search, ?array $filters): mixed
    {
        $leads = Lead::where('company_id', $company_id);
        if (! empty($search)) {
            $words = explode(' ', $search);
            if (count($words) == 1) {
                $leads->where('name', 'LIKE', "%$search%")
                    ->orWhere('business_name', 'LIKE', "%$search%")
                    ->orWhere('tags', 'LIKE', "%$search%");
            } else {
                $leads->whereFullText(['name', 'business_name'], $search)
                    ->orWhere('tags', 'LIKE', "%$search%");
            }
        }

        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $leads->where($key, $filter);
            }
        }

        return $leads->orderBy('created_at', 'desc')->paginate(10);
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

    public static function getStatus(): array
    {
        return [
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In progress',
            self::CONVERTED => 'Converted',
            self::CLOSED => 'Closed',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new AssignedSellerScope);
    }
}
