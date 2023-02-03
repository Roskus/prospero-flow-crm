<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use App\Models\Industry;
use Illuminate\Http\Request;
use Squire\Models\Country;

class CustomerUpdateController extends MainController
{
    /**
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, int $id)
    {
        $customer = Customer::find($id);
        $industry = new Industry();
        $data['customer'] = $customer;
        $data['countries'] = Country::orderBy('name')->get();
        $data['industries'] = $industry->getAll();

        return view('customer.customer', $data);
    }
}
