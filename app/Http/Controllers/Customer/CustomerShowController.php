<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class CustomerShowController extends Controller
{
    public function show(Customer $customer)
    {
        return view('customer.show', compact('customer'));
    }
}
