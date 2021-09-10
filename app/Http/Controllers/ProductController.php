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
        $product->company_id = Auth::user()->company_id;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->name = $request->name;
        $product->quantity = !empty($request->quantity) ? $request->quantity : 0;
        $product->cost = $request->cost;
        $product->price = $request->price;
        $product->barcode = $request->barcode;
        $product->sku = $request->sku;
        $product->description = $request->description;
        $product->save();

        $photoFile = $request->file('photo');
        if (!empty($photoFile)) {
            // Generate a new random file name
            $uuid = Uuid::uuid4();
            $newFileName = $uuid->toString().'.'.$photoFile->getClientOriginalExtension();
            // Create a path for product photo
            $destinationPath = public_path("asset/upload/product/$product->id");
            try {
                if (!is_dir($destinationPath)) {
                    $created = mkdir($destinationPath, 0775, true);
                } else {
                    $created = true;
                }


                if ($created) {
                    // Always use copy don't try to move in some servers have a issue with permisions and temporary directories.
                    copy($photoFile->getRealPath(), $destinationPath.DIRECTORY_SEPARATOR.$newFileName);

                    // Updating product object
                    $product->photo = $newFileName;
                    $product->updated_at = now();
                    $product->save();
                }
            } catch (Throwable $t) {
                //can't create directory
                Log::error($t->getMessage());
            }
        }
        return redirect('/product')->with('message', '');
    }
}
