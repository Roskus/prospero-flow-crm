<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderShowController extends MainController
{
    public function show(Request $request, int $order_number)
    {
        $order = Order::where('company_id', $request->user()->company_id)
            ->where('order_number', $order_number)
            ->firstOrFail();
        $data['order'] = $order;

        return view('order.show', $data);
    }
}
