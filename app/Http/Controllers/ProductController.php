<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends MainController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->category = new Category();
        $this->brand = new Brand();
    }

    public function index(Request $request)
    {
        $product = new Product();
        $data['products'] = $product->getAllByCompanyId(Auth::user()->company_id);
        $data['categories'] = $this->category->getAllActiveByCompany(Auth::user()->company_id);
        $data['brands'] = $this->brand->getAllActiveByCompany(Auth::user()->company_id);

        return view('product/index', $data);
    }
}
