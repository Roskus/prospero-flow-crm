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

        if ($invoicedMessage = $order->invoicedMessage()) {
            return redirect('order')->with(['status' => 'error', 'message' => $invoicedMessage]);
        }
        $order->status = Order::CONFIRMED;
        $order->save();

        return redirect('order');
    }
}
