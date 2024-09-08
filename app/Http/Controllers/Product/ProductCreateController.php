<?php

declare(strict_types=1);

namespace App\Http\Controllers\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCreateController
{
    public function create(Request $request)
    {
        $product = new Product;
        $category = new Category;
        $brand = new Brand;
        $data['product'] = $product;
        $data['products'] = $product->getAllByCompanyId((int) Auth::user()->company_id);
        $data['categories'] = $category->getAllActiveByCompany((int) Auth::user()->company_id);
        $data['brands'] = $brand->getAllActiveByCompany((int) Auth::user()->company_id);

        return view('product/product', $data);
    }
}
