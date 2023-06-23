<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;
use Squire\Models\Country;

/**
 *  @OA\Schema(
 *    schema="Company",
 *    type="object",
 *    required={"name", "country_id"},
 *    @OA\Property(
 *        property="name",
 *        description="Name of the company",
 *        type="string",
 *        example="My Company"
 *    ),
 *    @OA\Property(
 *        property="phone",
 *        description="Phone of the company",
 *        type="string",
 *        example="+34645600000"
 *    ),
 *    @OA\Property(
 *        property="email",
 *        description="Email of the company",
 *        type="string",
 *        format="email",
 *        example="info@company.com"
 *    ),
 *    @OA\Property(
 *        property="country_id",
 *        description="Country ISO code of the company",
 *        type="string",
 *        example="ES"
 *    ),
 *    @OA\Property(
 *        property="province",
 *        description="Province of the company",
 *        type="string",
 *        example="Barcelona"
 *    ),
 *    @OA\Property(
 *        property="city",
 *        description="City of the company",
 *        type="string",
 *        example="Barcelona"
 *    ),
 *    @OA\Property(
 *        property="street",
 *        description="Street of the company",
 *        type="string",
 *        example="Av. Constitución 123"
 *    ),
 *    @OA\Property(
 *        property="zipcode",
 *        description="Zipcode of the company",
 *        type="string",
 *        example="08860"
 *    ),
 *    @OA\Property(
 *        property="currency",
 *        description="Default currency of the company",
 *        type="string",
 *        example="USD"
 *    ),
 *    @OA\Property(
 *        property="website",
 *        description="Website of the company",
 *        type="string",
 *        example="https://www.company.com"
 *    )
 * )
 */
class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    const ACTIVE = 1;

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
        'street',
        'zipcode',
        'currency',
        'website',
        'updated_at',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $with = ['country'];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['name'],
        );
    }

    public function getAllPaginated(int $limit = 50)
    {
        return Company::orderBy('name', 'asc')->paginate($limit);
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
}
