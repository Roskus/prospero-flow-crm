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
     * @param  int  $company_id
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

    /**
     * @param int $company_id
     * @return array
     */
    public static function getAllActiveAsArrayByCompany(int $company_id) : array
    {
        return Brand::where('company_id', $company_id)
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * @param array $brands
     * @param string $name
     * @return int|null
     */
    public static function getBrandIdByName(array $brands, string $name) : ?int
    {
        foreach ($brands as $brand)
        {
            if ($brand['name'] == $name) {
                return (int) $brand['id'];
            }
        }
        return null;
    }
}
