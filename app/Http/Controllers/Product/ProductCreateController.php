<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class ProductCreateController
{
    public function create(Request $request)
    {
        $product = new Product();
        $category = new Category();
        $brand = new Brand();
        $data['product'] = $product;
        $data['products'] = $product->getAllByCompanyId(Auth::user()->company_id);
        $data['categories'] = $category->getAllActiveByCompany(Auth::user()->company_id);
        $data['brands'] = $brand->getAllActiveByCompany(Auth::user()->company_id);
        return view('product/product', $data);
    }
}
