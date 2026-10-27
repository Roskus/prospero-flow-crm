<?php

declare(strict_types=1);

namespace App\Http\Controllers\Product;

use App\Helpers\CurrencyHelper;
use App\Http\Controllers\MainController;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProductIndexController extends MainController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = (new Product)->searchByCompany((int) Auth::user()->company_id, $request->term)
                ->get(['id', 'name as label', 'price', 'tax', 'currency']);

            if ($request->filled('currency')) {
                $products = $products->map(fn (Product $product): array => $this->inCurrency($product, strtoupper((string) $request->currency)));
            }

            return response()->json($products);
        }

        $filters = [];
        $search = $request->search;
        $product = new Product;
        $category = new Category;
        $brand = new Brand;
        $data['search'] = $search;
        $data['product_count'] = Product::where('company_id', (int) Auth::user()->company_id)->count();
        $data['products'] = $product->getAllByCompanyId((int) Auth::user()->company_id, $search, $filters);
        $data['categories'] = $category->getAllActiveByCompany((int) Auth::user()->company_id);
        $data['brands'] = $brand->getAllActiveByCompany((int) Auth::user()->company_id);

        return view('product/index', $data);
    }

    /**
     * The autocomplete row with the price converted to the order currency, or no price when the company has no rate to convert it.
     *
     * @return array<string, mixed>
     */
    private function inCurrency(Product $product, string $currency): array
    {
        $row = $product->toArray();

        if ($product->currency !== $currency) {
            $row['label'] = $product->getAttribute('label').' ('.CurrencyHelper::formatIn($product->price, $product->currency).')';

            try {
                $row['price'] = app(ExchangeRateService::class)->convert((string) $product->price, $product->currency, $currency, Auth::user()->company);
            } catch (ValidationException) {
                $row['price'] = null;
            }
        }

        return $row;
    }
}
