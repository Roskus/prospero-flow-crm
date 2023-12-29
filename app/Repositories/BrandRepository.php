<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class BrandRepository
{
    public function save(array $data): ?Brand
    {
        if (empty($data['id'])) {
            $brand = new Brand();
            $brand->created_at = now();
        } else {
            $brand = Brand::find($data['id']);
        }
        $brand->name = $data['name'];
        $brand->company_id = Auth::user()->company_id;
        $brand->updated_at = now();
        $brand->save();

        return $brand;
    }
}
