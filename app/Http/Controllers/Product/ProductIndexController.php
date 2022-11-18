<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\MainController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductIndexController extends MainController
{
    public function index(Request $request)
    {
        $product = new Product();
        $category = new Category();
        $brand = new Brand();
        $data['products'] = $product->getAllByCompanyId(Auth::user()->company_id);
        $data['categories'] = $category->getAllActiveByCompany(Auth::user()->company_id);
        $data['brands'] = $brand->getAllActiveByCompany(Auth::user()->company_id);

        return view('product/index', $data);
    }
}
