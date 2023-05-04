<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class CustomerUpdateController extends MainController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, int $id)
    {
        $customer = Customer::find($id);
        $industry = new Industry();
        $user = new User();
        $data['customer'] = $customer;
        $data['countries'] = Country::orderBy('name')->get();
        // Temporary fix get this from configuration
        $data['industries'] = (Auth::user()->company_id == 3) ? $industry->getAllByCompany(Auth::user()->company_id) : $industry->getAll();
        $data['sellers'] = $user->getAllActiveByCompany(Auth::user()->company_id);
        $data['editorType'] = 'advanced';

        return view('customer.customer', $data);
    }
}
