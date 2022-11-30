<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class ProductImportIndexController extends MainController
{
    public function index(Request $request)
    {
        return view('product.import');
    }
}
