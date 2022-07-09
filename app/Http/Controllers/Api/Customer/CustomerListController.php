<?php

namespace App\Http\Controllers\Api\Customer;

use Illuminate\Http\Request;
use App\Http\Resources\CustomerResource;
use App\Models\Lead;

class CustomerListController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return CustomerResource::collection(Lead::all());
    }
}
