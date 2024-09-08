<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $order = Order::find($id);
        $product = new Product;
        $data['order'] = $order;
        $company_id = Auth::user()->company_id;
        $data['customers'] = $order->customer->getAllByCompanyId($company_id);
        $data['products'] = $product->getAllByCompanyId($company_id);

        return view('order/order', $data);
    }
}
