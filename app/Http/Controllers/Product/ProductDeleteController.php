<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $product = Product::find($id);
        $product->delete();

        return redirect('/product');
    }
}
