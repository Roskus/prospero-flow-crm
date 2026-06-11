<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\MainController;
use App\Models\Account;
use App\Models\Account\Category;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountCreateController extends MainController
{
    public function create(Request $request)
    {
        $companyId = (int) Auth::user()->company_id;

        return view('account.account', [
            'account' => new Account,
            'categories' => Category::where('company_id', $companyId)->orderBy('name')->get(),
            'customers' => Customer::where('company_id', $companyId)->orderBy('name')->get(),
            'suppliers' => Supplier::where('company_id', $companyId)->orderBy('name')->get(),
        ]);
    }
}
