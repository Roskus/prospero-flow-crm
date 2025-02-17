<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Supplier;

use App\Http\Requests\API\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
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
     *              @OA\Property(property="email", type="string", format="email", example="supplier@sony.com"),
     *              @OA\Property(property="phone", type="string", example="+1234567890"),
     *              @OA\Property(property="country", type="string", example="UK"),
     *              @OA\Property(property="business_name", type="string", example="Sony Corporation"),
     *              @OA\Property(property="vat", type="string", example="GB123456789"),
     *              @OA\Property(property="website", type="string", example="https://www.sony.com"),
     *              @OA\Property(property="province", type="string", example="London"),
     *              @OA\Property(property="city", type="string", example="London"),
     *              @OA\Property(property="street", type="string", example="123 Main St"),
     *              @OA\Property(property="zipcode", type="string", example="W1A 1AA")
     *          )
     *      ),
     *      @OA\Response(response="201", description="Supplier created successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     */
    public function create(SupplierRequest $request): JsonResponse
    {
        $data = [];
        try {
            $supplier = new Supplier;
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

            return response()->json($data, $status);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: '.$e->getMessage(),
            ], 500);
        }
    }
}
