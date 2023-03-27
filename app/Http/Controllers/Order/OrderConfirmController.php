<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderConfirmController extends MainController
{
    public function confirm(Request $request, int $id)
    {
        $order = Order::find($id);
        $order->status = Order::CONFIRMED;
        $order->save();
        return redirect('order');
    }
}
