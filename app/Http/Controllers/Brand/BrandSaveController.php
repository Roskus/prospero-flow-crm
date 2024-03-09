<?php

declare(strict_types=1);

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\MainController;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandSaveController extends MainController
{
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $brand = new Brand();
        } else {
            $brand = Brand::find($request->id);
        }
        $brand->name = $request->name;
        $brand->company_id = (int) Auth::user()->company_id;
        $brand->save();

        return redirect('/brand');
    }
}
