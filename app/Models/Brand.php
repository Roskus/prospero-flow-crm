<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brand';

    public function getAll()
    {
        return Brand::orderBy('name', 'asc')->get();
    }

    /**
     * @param int $company_id
     * @return mixed
     */
    public function getAllByCompanyId(int $company_id)
    {
        return Brand::where('company_id', $company_id)->get();
    }

    public function getAllActiveByCompany(int $company_id)
    {
        return Brand::where('company_id', $company_id)->get();
    }
}
