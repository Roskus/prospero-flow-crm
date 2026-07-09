<?php

declare(strict_types=1);

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\MainController;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $brand = Brand::where('company_id', (int) Auth::user()->company_id)->findOrFail($id);
        $data['brand'] = $brand;

        return view('brand.brand', $data);
    }
}
