<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderCreateController extends MainController
{
    /**
     * Add new order
     */
    public function create(Request $request)
    {
        $order = new Order;
        $customer = new Customer;
        $product = new Product;
        $company_id = (int) Auth::user()->company_id;
        $data['order'] = $order;
        $data['customers'] = $customer->getAllByCompanyId($company_id);
        $data['products'] = $product->getAllByCompanyId($company_id);

        $data['branches'] = Auth::user()->company->hasSeveralBranches()
            ? Branch::ofCompany((int) Auth::user()->company_id)->orderByDesc('is_main')->orderBy('code')->get()
            : collect();

        $data['currencies'] = Auth::user()->company->usableCurrencies();

        return view('order/order', $data);
    }
}
