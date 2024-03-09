<?php

declare(strict_types=1);

namespace App\Http\Controllers\Product;

use App\Http\Controllers\MainController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductImportSaveController extends MainController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        if (! $request->hasFile('upload')) {
            return redirect('/product')->withErrors(__("Upload file can't be in blank"));
        }
        $categories = Category::getAllActiveAsArrayByCompany((int) Auth::user()->company_id);
        $brands = Brand::getAllActiveAsArrayByCompany((int) Auth::user()->company_id);
        $file = $request->file('upload');
        $extension = $file->getClientOriginalExtension();

        $filePath = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();

        try {
            $handle = fopen($filePath, 'r');
        } catch (\Throwable $t) {
            return redirect('/product')->withErrors(__("Can't read uploaded file"));
        }

        //name;category;brand;model;sku;barcode;cost;price;description;tags

        $rowCount = 0;
        $separator = ';'; //(! empty($request->separator)) ? $request->separator : ';';
        while (($data = fgetcsv($handle, 2000, $separator)) !== false) {
            //Skip header starting in 1

            if ($data[0] == 'name') {
                continue;
            }

            // if row is empty continue
            if (empty($data[0])) {
                continue;
            }

            $product = new Product();
            $product->company_id = Auth::user()->company_id;
            $product->category_id = Category::getCategoryIdByName($categories, $data[1]);
            $product->brand_id = Brand::getBrandIdByName($brands, $data[2]);
            $product->name = $data[0];
            $product->model = $data[3];
            $product->sku = $data[4];
            $product->barcode = $data[5];
            $product->cost = (float) $data[6];
            $product->price = (float) $data[7];
            $product->description = $data[8];
            $product->tags = null; //$data[9];
            $product->created_at = now();
            $product->tax = 0;

            try {
                $product->save();
                $rowCount++;
            } catch (\Throwable $t) {
                Log::error($t->getMessage().' | row number:'.($rowCount + 1));
            }
        }
        fclose($handle);
        $status = ($rowCount > 0) ? true : false;

        $response = [
            'status' => $status,
            'message' => ($status) ? 'Products imported :count successfully' : 'An error occurred while importing products',
            'count' => $rowCount,
        ];

        return redirect('/product')->with($response);
    }
}
