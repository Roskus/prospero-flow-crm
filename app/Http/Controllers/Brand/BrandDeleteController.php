<?php

declare(strict_types=1);

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\MainController;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $brand = Brand::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $brand->delete();

        return redirect('/brand');
    }
}
