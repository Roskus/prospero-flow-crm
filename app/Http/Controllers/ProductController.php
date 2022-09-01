<?php
namespace App\Http\Controllers;

use Throwable;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;

class ProductController extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $this->category = new Category();
        $this->brand = new Brand();
    }

    public function index(Request $request)
    {
        $product = new Product();
        $data['products'] = $product->getAllByCompanyId(Auth::user()->company_id);
        $data['categories'] = $this->category->getAllActiveByCompany(Auth::user()->company_id);
        $data['brands'] = $this->brand->getAll();
        return view('product/index', $data);
    }

    public function add(Request $request)
    {
        $product = new Product();
        $data['product'] = $product;
        $data['categories'] = $this->category->getAllActiveByCompany(Auth::user()->company_id);
        $data['brands'] = $this->brand->getAllByCompanyId(Auth::user()->company_id);
        return view('product/product', $data);
    }

    public function edit(Request $request,$id)
    {
        $product = Product::find($id);
        $data['product'] = $product;
        $data['categories'] = $this->category->getAll();
        $data['brands'] = $this->brand->getAll();
        return view('product/product', $data);
    }
}
