<?php

declare(strict_types=1);

namespace App\Http\Controllers\Product;

use App\Http\Controllers\MainController;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Throwable;

class ProductSaveController extends MainController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function save(ProductRequest $request)
    {
        $product = $this->productRepository->save($request->validated());

        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            // Generate a new random file name
            $uuid = Uuid::uuid4();
            $newFileName = $uuid->toString().'.'.$photoFile->getClientOriginalExtension();
            // Create a path for product photo
            $destinationPath = public_path("asset/upload/product/$product->id");
            try {
                if (! is_dir($destinationPath)) {
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
        $message = ($product) ? 'Product saved successfully' : 'An error occurs saving the product';

        return redirect('/product')->with('message', $message);
    }
}
