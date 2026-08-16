<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\SecurityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderRepository
{
    public function __construct(private SecurityLogger $securityLogger) {}

    public function save(array $data): ?Order
    {
        DB::beginTransaction();

        try {
            $companyId = (int) Auth::user()->company_id;
            $canOverridePrice = Auth::user()->can('override order price');

            if (empty($data['id'])) {
                $order = new Order;
                $order->created_at = now();
            } else {
                $order = Order::where('company_id', $companyId)->findOrFail($data['id']);
                $order->items()->delete();
            }

            if (! empty($data['customer_id'])) {
                Customer::where('company_id', $companyId)->findOrFail($data['customer_id']);
            }

            $order->setCompanyId($companyId);
            $order->setCustomerId((int) $data['customer_id']);
            $order->seller_id = ! empty($data['seller_id']) ? $data['seller_id'] : Auth::user()->id;

            $order->setAmount((float) $order->getTotal());
            $order->save();

            if (! empty($data['items'])) {
                $productIds = collect($data['items'])
                    ->pluck('product_id')
                    ->map(fn (mixed $id): int => (int) $id)
                    ->unique()
                    ->all();

                $products = Product::where('company_id', $companyId)
                    ->whereIn('id', $productIds)
                    ->get()
                    ->keyBy('id');

                foreach ($data['items'] as $requestItem) {
                    $productId = (int) $requestItem['product_id'];
                    $product = $products->get($productId);

                    if ($product === null) {
                        throw ValidationException::withMessages([
                            'items' => __('The selected product is not available for your company.'),
                        ]);
                    }

                    $unitPrice = $canOverridePrice
                        ? (float) $requestItem['price']
                        : (float) $product->price;

                    if ($canOverridePrice && $unitPrice !== (float) $product->price) {
                        $this->securityLogger->log('order_price_override', [
                            'order_id' => $order->id,
                            'product_id' => $productId,
                            'catalogue_price' => (float) $product->price,
                            'overridden_price' => $unitPrice,
                            'quantity' => (int) $requestItem['quantity'],
                        ]);
                    }

                    $item = new Order\Item;
                    $item->order_id = $order->id;
                    $item->order_number = $order->order_number;
                    $item->product_id = $productId;
                    $item->quantity = (int) $requestItem['quantity'];
                    $item->unit_price = $unitPrice;
                    $item->discount = (float) ($requestItem['discount'] ?? 0);
                    $item->tax = (float) ($requestItem['tax'] ?? 0);
                    $order->items()->save($item);
                }
            }

            DB::commit();

            return $order;
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t->getMessage());
            throw $t;
        }
    }
}
