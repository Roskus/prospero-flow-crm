<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderCancelController extends MainController
{
    /**
     * Pending and confirmed orders can be canceled; completed orders are invoiced.
     */
    public function cancel(Request $request, int $order_number): RedirectResponse
    {
        $order = Order::where('company_id', $request->user()->company_id)
            ->where('order_number', $order_number)
            ->firstOrFail();

        if (! in_array((int) $order->status, [Order::PENDING, Order::CONFIRMED], true)) {
            return redirect('order')->with('error', __('Only pending or confirmed orders can be canceled'));
        }

        $order->status = Order::CANCELED;
        $order->save();

        return redirect('order');
    }
}
