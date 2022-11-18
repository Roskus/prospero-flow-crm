<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderSaveController extends MainController
{

    /**
     * @param  Request  $request
     */
    public function save(Request $request)
    {
        $order = new Order();
        $order->setCompanyId((int) Auth::user()->company_id);
        $order->setCustomerId((int) $request->customer_id);
        $order->setAmount((float) $request->total);
        try {
            $status = $order->save();
        } catch (\Throwable $t) {
            Log::error($t->getMessage());
        }

        if ($request->items) {
            foreach ($request->items as $requestItem) {
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

        return redirect('order');
    }
}
