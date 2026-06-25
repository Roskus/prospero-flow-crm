<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class CustomerDeleteController extends MainController
{
    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function delete(Request $request, int $id)
    {
        $customer = Customer::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();
        $customer->delete();

        return redirect('/customer')->with(['status' => true, 'message' => __('Customer deleted successfully')]);
    }
}
