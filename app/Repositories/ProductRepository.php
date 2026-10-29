<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\DimensionUnit;
use App\Enums\WeightUnit;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductRepository
{
    public function save(array $data): ?Product
    {
        if (empty($data['id'])) {
            $product = new Product;
            $product->created_at = now();
        } else {
            $product = Product::where('company_id', Auth::user()->company_id)->findOrFail($data['id']);
        }
        $product->company_id = Auth::user()->company_id;
        $product->category_id = ! empty($data['category_id']) ? $data['category_id'] : null;
        $product->brand_id = ! empty($data['brand_id']) ? $data['brand_id'] : null;
        $product->name = $data['name'];
        $product->quantity = ! empty($data['quantity']) ? $data['quantity'] : 0;
        $product->min_stock_quantity = ! empty($data['min_stock_quantity']) ? $data['min_stock_quantity'] : 0;
        $product->weight = isset($data['weight']) && $data['weight'] !== '' ? $data['weight'] : null;
        $product->weight_unit = ! empty($data['weight_unit']) ? $data['weight_unit'] : WeightUnit::Gram->value;
        $product->dimensions = ! empty($data['dimensions']) ? $data['dimensions'] : null;
        $product->dimension_unit = ! empty($data['dimension_unit']) ? $data['dimension_unit'] : DimensionUnit::Millimeter->value;
        $product->cost = $data['cost'];
        $product->price = $data['price'];
        $product->currency = ! empty($data['currency']) ? $data['currency'] : ($product->exists ? $product->currency : Auth::user()->company->currency);
        $product->tax = ! empty($data['tax']) ? $data['tax'] : 0.0;
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
