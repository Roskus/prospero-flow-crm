<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRepository
{
    public function save(array $data): ?Order
    {
        DB::beginTransaction();

        if (empty($data['id'])) {
            $order = new Order();
            $company_id = Auth::user()->company_id;
            $last_order_number = OrderNumber::where('company_id', $company_id)
                ->lockForUpdate()
                ->value('last_order_number');

            $new_order_number = $last_order_number + 1;

            OrderNumber::where('company_id', $company_id)
                ->update(['last_order_number' => $new_order_number]);
            $order->order_number = $new_order_number;
            $order->created_at = now();
        } else {
            $order = Order::find($data['id']);
        }

        $order->setCompanyId((int) Auth::user()->company_id);
        $order->setCustomerId((int) $data['customer_id']);
        $order->seller_id = ! empty($data['seller_id']) ? $data['seller_id'] : Auth::user()->id;

        $total = $order->getTotal();

        $order->setAmount((float) $total);
        try {
            $status = $order->save();
            DB::commit();
        } catch (\Throwable $t) {
            DB::rollBack();
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
