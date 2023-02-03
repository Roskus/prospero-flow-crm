<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class CustomerImportIndexController extends MainController
{
    public function index(Request $request)
    {
        return view('customer.import');
    }
}
