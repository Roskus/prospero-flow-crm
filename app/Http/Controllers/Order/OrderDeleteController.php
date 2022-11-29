<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderDeleteController extends MainController
{
    /**
     * @param  Request  $request
     * @param  int  $id
     */
    public function delete(Request $request, int $id)
    {
        $order = Order::find($id);
        $order->delete();

        return redirect('order');
    }
}
