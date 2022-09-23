<?php

namespace App\Http\Controllers\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

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
