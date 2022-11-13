<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Squire\Models\Country;
use OpenApi\Annotations\OpenApi as OA;

/**
 *  @OA\Schema(
 *    schema="Lead",
 *    type="object",
 *
 *    @OA\Property(
 *      property="company_id",
 *      type="number",
 *      example="1"
 *    ),
 *    @OA\Property(
 *        property="name",
 *        description="Name of the company",
 *        type="string",
 *        example="John"
 *    )
 * )
 */
class Lead extends Model
{
    use HasFactory;
    use SoftDeletes;

    const OPEN = 'open'; //New

    const IN_PROGRESS = 'in_progress';

    const CONVERTED = 'converted'; // Promoted to customer

    const CLOSED = 'closed';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lead';

    protected $fillable = [
        'company_id',
        'name',
        'business_name',
        'dob',
        'vat',
        'phone',
        'mobile',
        'email',
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
        'opt_in',
        'tags',
        'status',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

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

    /**
     * @param  int  $company_id
     * @param  string|null  $search
     * @param  array|null  $filters
     * @return mixed
     */
    public function getAllByCompanyId(int $company_id, ?string $search, ?array $filters)
    {
        $leads = Lead::where('company_id', $company_id);
        if (! empty($search)) {
            $leads->where('name', 'LIKE', "%$search%")
                  ->orWhere('tags', 'LIKE', "%$search%");
        }

        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $leads->where($key, $filter);
            }
        }

        return $leads->with('seller', 'industry')->paginate(10);
    }

    /**
     * @param  int  $company_id
     * @return int
     */
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
}
