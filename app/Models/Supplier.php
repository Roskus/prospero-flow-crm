<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Squire\Models\Country;

/**
 *  @OA\Schema(
 *    schema="Supplier",
 *    type="object",
 *    required={"name"},
 *  )
 */
class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'company_id',
        'name',
        'business_name',
        'vat',
        'phone',
        'email',
        'website',
        'country_id',
        'province',
        'city',
        'street',
        'zipcode',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getAllByCompany(int $company_id)
    {
        return Supplier::where('company_id', $company_id)->get();
    }
}
