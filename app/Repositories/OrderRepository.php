<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderRepository
{
    public function save(array $data): ?Order
    {
        if (empty($data['id'])) {
            $order = new Order();
            $order->created_at = now();
        } else {
            $order = Order::find($data['id']);
        }

        $order->setCompanyId((int) Auth::user()->company_id);
        $order->setCustomerId((int) $data['customer_id']);
        $order->seller_id = ! empty($data['seller_id']) ? $data['seller_id'] : Auth::user()->id;

        $items = $order->items;
        $total = $items->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        $order->setAmount((float) $total);
        try {
            $status = $order->save();
        } catch (\Throwable $t) {
            Log::error($t->getMessage());
        }

        if (! empty($data['items'])) {
            foreach ($data['items'] as $requestItem) {
                try {
                    $item = new Order\Item();
                    $item->order_id = $order->id;
                    $item->product_id = $requestItem['product_id'];
                    $item->quantity = $requestItem['quantity'];
                    $item->unit_price = $requestItem['price'];
                    $order->items()->save($item);
                } catch (\Throwable $t) {
                    Log::error($t->getMessage());
                }
            }
        }

        return $order;
    }
}
