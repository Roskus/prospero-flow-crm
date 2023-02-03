<?php

declare(strict_types=1);

namespace App\Http\Controllers\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductUpdateController
{
    public function update(Request $request, int $id)
    {
        $product = Product::find($id);
        $category = new Category();
        $brand = new Brand();
        $data['product'] = $product;
        $data['categories'] = $category->getAllActiveByCompany(Auth::user()->company_id);
        $data['brands'] = $brand->getAllActiveByCompany(Auth::user()->company_id);

        return view('product/product', $data);
    }
}
