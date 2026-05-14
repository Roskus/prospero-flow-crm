<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class CustomerDeleteController extends MainController
{
    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function delete(Request $request, int $id)
    {
        $customer = Customer::find($id);
        $customer->delete();

        return redirect('/customer')->with(['status' => true, 'message' => __('Customer deleted successfully')]);
    }
}
