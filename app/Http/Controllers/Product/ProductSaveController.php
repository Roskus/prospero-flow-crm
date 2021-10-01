<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\MainController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ProductSaveController extends MainController
{
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
