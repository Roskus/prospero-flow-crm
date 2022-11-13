<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations\OpenApi as OA;
use Squire\Models\Country;

/**
 *  @OA\Schema(
 *    schema="Customer",
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
class Customer extends Model
{
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
    protected $table = 'customer';

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
        return $this->hasMany(\App\Models\Contact::class, 'customer_id', 'id');
    }

    public function getAll()
    {
        return Customer::all();
    }

    /**
     * @param  int  $company_id
     * @param  string|null  $search
     * @param  array|null  $filters
     * @return mixed
     */
    public function getAllByCompanyId(int $company_id, ?string $search = null, ?array $filters = null)
    {
        $customers = Customer::where('company_id', $company_id);
        if (! empty($search)) {
            $customers->where('name', 'LIKE', "%$search%")
                      ->orWhere('tags', 'LIKE', "%$search%");
        }

        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $customers->where($key, $filter);
            }
        }

        return $customers->with('seller', 'industry')->paginate(10);
    }

    public function getCountByCompany(int $company_id): int
    {
        return Customer::where('company_id', $company_id)->count();
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
