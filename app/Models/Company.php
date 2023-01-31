<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'logo',
        'phone',
        'email',
        'country_id',
        'website',
        'status',
    ];

    public function getAll()
    {
        return Company::orderBy('name', 'asc')->get();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
