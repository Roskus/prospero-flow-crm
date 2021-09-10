<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    const ACTIVE = 1;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'customer';

    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
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
