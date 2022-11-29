<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\MainController;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $brand = Brand::find($id);
        $brand->delete();

        return redirect('/brand');
    }
}
