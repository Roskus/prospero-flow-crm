<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends MainController
{
    public function index()
    {
        $product = new Product();
        $data['products'] = $product->getAll();
        return view('product/index',$data);
    }
}