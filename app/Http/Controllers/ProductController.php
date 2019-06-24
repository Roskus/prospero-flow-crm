<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class ProductController extends MainController
{
    public function index()
    {
        $category = new Category();
        $product = new Product();
        $data['products'] = $product->getAll();
        $data['categories'] = $category->getAll();
        return view('product/index',$data);
    }

    public function add()
    {
        $category = new Category();
        $product = new Product();
        $data['product'] = $product;
        $data['categories'] = $category->getAll();
        return view('product/product',$data);
    }

    public function edit(Request $request,$id)
    {
        $category = new Category();
        $product = Product::find($id);
        $data['product'] = $product;
        $data['categories'] = $category->getAll();
        return view('product/product',$data);
    }

    public function save(Request $request)
    {
        if(empty($request->id)) {
            $product = new Product();
        } else {
            $product = Product::find($request->id);
        }
        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->quantity = $request->quantity;
        $product->price = $request->price;
        $product->save();
        return redirect('/product');
    }
}
