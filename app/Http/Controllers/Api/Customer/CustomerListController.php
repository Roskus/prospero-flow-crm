<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Resources\CustomerResource;
use App\Models\Lead;
use Illuminate\Http\Request;

class CustomerListController
{
    /**
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return CustomerResource::collection(Lead::all());
    }
}
