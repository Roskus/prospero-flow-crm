<?php

declare(strict_types=1);

namespace App\Http\Controllers\Product;

use App\Http\Controllers\MainController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $product = Product::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $category = new Category;
        $brand = new Brand;
        $data['product'] = $product;
        $data['categories'] = $category->getAllActiveByCompany(Auth::user()->company_id);
        $data['brands'] = $brand->getAllActiveByCompany(Auth::user()->company_id);

        return view('product/product', $data);
    }
}
