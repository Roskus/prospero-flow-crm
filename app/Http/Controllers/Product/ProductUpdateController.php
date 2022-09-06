<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class ProductUpdateController
{
    public function update(Request $request, int $id)
    {
        $product = Product::find($id);
        $category = new Category();
        $brand = new Brand();
        $data['product'] = $product;
        $data['categories'] = $category->getAll();
        $data['brands'] = $brand->getAll();
        return view('product/product', $data);
    }
}
