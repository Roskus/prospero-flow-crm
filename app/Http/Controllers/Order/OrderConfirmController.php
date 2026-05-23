<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderConfirmController extends MainController
{
    public function confirm(Request $request, int $order_number)
    {
        $order = Order::where('company_id', $request->user()->company_id)
            ->where('order_number', $order_number)
            ->firstOrFail();
        $order->status = Order::CONFIRMED;
        $order->save();

        return redirect('order');
    }
}
