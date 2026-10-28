<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderPendingController extends MainController
{
    /**
     * A confirmed order goes back to pending so it can be edited again.
     */
    public function pending(Request $request, int $order_number): RedirectResponse
    {
        $order = Order::where('company_id', $request->user()->company_id)
            ->where('order_number', $order_number)
            ->firstOrFail();

        if ((int) $order->status !== Order::CONFIRMED) {
            return redirect('order')->with(['status' => 'error', 'message' => __('Only confirmed orders can go back to pending')]);
        }

        $order->status = Order::PENDING;
        $order->save();

        return redirect('order');
    }
}
