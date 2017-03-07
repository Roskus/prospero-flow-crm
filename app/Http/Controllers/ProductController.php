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
  
    public function add()
    {
        $product = new Product();
        $data['product'] = $product;
        return view('product/product',$data);
    }
  
    public function edit(Request $request,$id)
    {
        $product = Product::find($id);
        $data['product'] = $product;
        return view('product/product',$data);
    }
  
    public function save(Request $request)
    {
        if(empty($request->id)) {
            $product = new Product();
        } else {
            $product = Product::find($request->id);
        }
        $product->name = $request->name;
        $product->quantity = $request->quantity;
        $product->price = $request->price;
        $product->save();
        return redirect('/product');
    }
}