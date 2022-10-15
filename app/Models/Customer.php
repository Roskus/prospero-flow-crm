<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    const ACTIVE = 1;

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
        'country',
        'province',
        'city',
        'locality',
        'street',
        'zipcode',
        'schedule_contact',
        'industry_id',
        'opt_in',
        'status',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    public function company()
    {
        return $this->hasOne(\App\Models\Company::class, 'id', 'company_id');
    }

    public function contacts()
    {
        return $this->hasMany(\App\Models\Contact::class, 'id', 'customer_id');
    }

    public function getAll()
    {
        return Customer::all();
    }

    public function getAllByCompanyId(int $company_id)
    {
        return Customer::where('company_id', $company_id)->get();
    }
}
