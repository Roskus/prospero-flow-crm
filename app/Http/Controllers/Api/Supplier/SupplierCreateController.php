<?php

namespace App\Http\Controllers\Api\Supplier;

use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierCreateController
{
    /**
     * @OA\Post (
     *      path="/supplier",
     *      summary="Create a Supplier",
     *      tags={"Supplier"},
     *      security={{"bearerAuth": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Supplier attributes",
     *          @OA\JsonContent(
     *              required={"name", "country"},
     *              @OA\Property(property="name", type="string", example="Sony"),
     *              @OA\Property(property="country", type="string", example="UK")
     *          )
     *      ),
     *      @OA\Response(response="201", description="Supplier created successfully"),
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
            'name' => ['required', 'max:50'],
            //'email' => ['max:254'],
            //'phone' => ['max:15'],
            'country' => ['required', 'max:2'],
        ]);

        if ($valid) {
            $supplier = new Supplier();
            $supplier->company_id = Auth::user()->company_id;
            $supplier->name = $request->name;
            $supplier->business_name = $request->business_name;
            $supplier->vat = $request->vat;
            $supplier->phone = $request->phone;
            $supplier->email = $request->email;
            $supplier->website = $request->website;
            $supplier->country_id = $request->country;
            $supplier->province = $request->province;
            $supplier->city = $request->city;
            $supplier->street = $request->street;
            $supplier->zipcode = $request->zipcode;
            $supplier->created_at = now();
            $supplier->save();
            $status = 201;
            $data['supplier'] = ['id' => $supplier->id];
        }

        return response()->json($data, $status);
    }
}
