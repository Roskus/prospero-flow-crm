<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;

class CustomerSaveController extends MainController
{
    private CustomerRepository $customerSaveRepository;

    public function __construct(CustomerRepository $customerSaveRepository)
    {
        $this->customerSaveRepository = $customerSaveRepository;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $status = 'error';
        $customer = $this->customerSaveRepository->save($request->all());

        if (! empty($customer)) {
            $status = 'success';
        }

        $response = [
            'status' => $status,
            'message' => 'Customer :name successfully saved',
            'name' => (! empty($customer)) ? $customer->name : '',
            'id' => (! empty($customer)) ? $customer->id : null,
        ];

        return redirect('/customer')->with($response);
    }
}
