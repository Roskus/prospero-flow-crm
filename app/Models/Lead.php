<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'country',
        'province',
        'city',
        'locality',
        'street',
        'zipcode',
        'schedule_contact',
        'industry_id',
        'opt_in',
        'status'
    ];
    protected $hidden = [
        'deleted_at'
    ];

    public function company()
    {
        return $this->hasOne(\App\Models\Company::class, 'id', 'company_id');
    }

    public function seller()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'seller_id');
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
        if (empty($search)) {
            $leads = Lead::where('company_id', $company_id);
        } else {
            $leads = Lead::where('name', 'LIKE', "%$search%");
        }

        if (is_array($filters)) {
            foreach ($filters as $key => $filter) {
                $leads->where($key, $filter);
            }
        }

        return $leads->paginate(10);
    }

    public function getCountByCompany(int $company_id): int
    {
        return Lead::where('company_id', $company_id)->count();
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
