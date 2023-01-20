<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerCreateController
{
    /**
     * @OA\Post (
     *      path="/customer",
     *      summary="Create a Customer",
     *      tags={"Customer"},
     *      security={{"bearerAuth": {} }},
     *      @OA\Response(response="201", description="Customer created successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function create(Request $request): JsonResponse
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'first_name' => ['required', 'max:50'],
            'email' => ['required', 'max:254'],
            'phone' => ['required', 'max:15'],
            'country_id' => ['required', 'max:2'],
        ]);

        if ($valid) {
            $customer = new Customer();
            $customer->company_id = Auth::user()->company_id;
            $customer->name = $request->name;
            $customer->business_name = $request->business_name;
            $customer->phone = $request->phone;
            $customer->mobile = $request->mobile;
            $customer->email = $request->email;
            $customer->website = $request->website;
            $customer->linkedin = $request->linkedin;
            $customer->tags = \trim($request->tags);
            $customer->created_at = now();
            $customer->save();
            $status = 201;
            $data['customer'] = ['id' => $customer->id];
        }

        return response()->json($data, $status);
    }
}
