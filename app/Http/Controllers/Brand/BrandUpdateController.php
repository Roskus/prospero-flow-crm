<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\MainController;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $brand = Brand::find($id);
        $data['brand'] = $brand;

        return view('brand.brand', $data);
    }
}
