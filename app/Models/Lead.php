<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    //use SoftDeletes;
    const ACTIVE = 1;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'lead';

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

    public function getAllByCompanyId(int $company_id, ?string $search = '')
    {
        if(empty($search))
        {
            $leads = Lead::where('company_id', $company_id)->paginate(10);
        } else {
            $leads = Lead::where("name","LIKE","%$search%")->paginate(10);
        }
        return $leads;
    }

    public function getCountByCompany(int $company_id) : int
    {
        return Lead::where('company_id', $company_id)->count();
    }

}
