<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerShowController extends MainController
{
    public function show(int $id)
    {
        $customer = Customer::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        return view('lead_customer.show', compact('customer'));
    }
}
