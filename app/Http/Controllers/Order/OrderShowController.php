<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderShowController extends MainController
{
    public function show(Request $request, int $id)
    {
        $order = Order::findOrFail($id);
        $data['order'] = $order;

        return view('order.show', $data);
    }
}
