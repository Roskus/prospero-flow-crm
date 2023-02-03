<?php

declare(strict_types=1);

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\MainController;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandCreateController extends MainController
{
    public function create(Request $request)
    {
        $brand = new Brand();
        $data['brand'] = $brand;

        return view('brand.brand', $data);
    }
}
