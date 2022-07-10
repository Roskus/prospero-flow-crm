<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
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
            $leads = Lead::where("first_name","LIKE","%$search%")->paginate(10);
        }
        return $leads;
    }

}
