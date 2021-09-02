<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
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
        $data['products'] = $product->getAll();
        $data['categories'] = $this->category->getAll();
        $data['brands'] = $this->brand->getAll();
        return view('product/index', $data);
    }

    public function add(Request $request)
    {
        $product = new Product();
        $data['product'] = $product;
        $data['categories'] = $this->category->getAll();
        $data['brands'] = $this->brand->getAll();
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

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $product = new Product();
        } else {
            $product = Product::find($request->id);
        }
        //@todo company_id
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->name = $request->name;
        $product->quantity = !empty($request->quantity) ? $request->quantity : 0;
        $product->cost = $request->cost;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->save();
        return redirect('/product')->with('message', '');
    }
}
