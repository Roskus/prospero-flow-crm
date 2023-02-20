<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductRepository
{
    public function save(array $data): ?Product
    {
        if (empty($data['id'])) {
            $product = new Product();
            $product->created_at = now();
        } else {
            $product = Product::find($data['id']);
        }
        $product->company_id = Auth::user()->company_id;
        $product->category_id = ! empty($data['category_id']) ? $data['category_id'] : null;
        $product->brand_id = ! empty($data['brand_id']) ? $data['brand_id'] : null;
        $product->name = $data['name'];
        $product->quantity = ! empty($data['quantity']) ? $data['quantity'] : 0;
        $product->min_stock_quantity = ! empty($data['min_stock_quantity']) ? $data['min_stock_quantity'] : 0;
        $product->cost = $data['cost'];
        $product->price = $data['price'];
        $product->barcode = ! empty($data['barcode']) ? $data['barcode'] : null;
        $product->sku = ! empty($data['sku']) ? $data['sku'] : null;
        $product->elaboration_date = ! empty($data['elaboration_date']) ? $data['elaboration_date'] : null;
        $product->expiration_date = ! empty($data['expiration_date']) ? $data['expiration_date'] : null;
        $product->description = ! empty($data['description']) ? $data['description'] : null;
        $product->updated_at = now();
        $product->save();

        return $product;
    }
}
