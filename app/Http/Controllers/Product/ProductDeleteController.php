<?php

declare(strict_types=1);

namespace App\Http\Controllers\Product;

use App\Http\Controllers\MainController;
use App\Http\Requests\ProductDeleteRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductDeleteController extends MainController
{
    public function delete(ProductDeleteRequest $request, int $id)
    {
        $product = Product::where('company_id', Auth::user()->company_id)->find($id);

        if ($product) {
            $product->delete();
        }

        return redirect('/product');
    }
}
