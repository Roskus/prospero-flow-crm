<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded = ['id'];

    protected $table = 'brand';

    public function getAll()
    {
        return Brand::orderBy('name', 'asc')->get();
    }

    /**
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

    public static function getAllActiveAsArrayByCompany(int $company_id): array
    {
        return Brand::where('company_id', $company_id)
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
    }

    public static function getBrandIdByName(array $brands, string $name): ?int
    {
        foreach ($brands as $brand) {
            if ($brand['name'] == $name) {
                return (int) $brand['id'];
            }
        }

        return null;
    }
}
