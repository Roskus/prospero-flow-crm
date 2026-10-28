<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderUpdateController extends MainController
{
    public function update(Request $request, int $order_number)
    {
        $order = Order::where('company_id', Auth::user()->company_id)
            ->where('order_number', $order_number)
            ->firstOrFail();

        if ($invoicedMessage = $order->invoicedMessage()) {
            return redirect('order')->with(['status' => 'error', 'message' => $invoicedMessage]);
        }
        $product = new Product;
        $data['order'] = $order;
        $company_id = Auth::user()->company_id;
        $data['customers'] = $order->customer->getAllByCompanyId($company_id);
        $data['products'] = $product->getAllByCompanyId($company_id);

        $data['branches'] = Auth::user()->company->hasSeveralBranches()
            ? Branch::ofCompany((int) Auth::user()->company_id)->orderByDesc('is_main')->orderBy('code')->get()
            : collect();

        $data['currencies'] = Auth::user()->company->usableCurrencies([$order->currency]);

        return view('order/order', $data);
    }
}
