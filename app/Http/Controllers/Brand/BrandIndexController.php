<?php

declare(strict_types=1);

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\MainController;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandIndexController extends MainController
{
    public function index(Request $request)
    {
        $brand = new Brand();
        $data['brands'] = $brand->getAllByCompanyId(Auth::user()->company_id);

        return view('brand.index', $data);
    }
}
