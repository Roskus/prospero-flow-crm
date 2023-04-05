<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerReadController
{
    /**
     * @OA\Get(
     *     path="/customer/{id}",
     *     summary="Get Customer information",
     *     tags={"Customer"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of the Customer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Customer found",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(response="404", description="Customer not found")
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request, int $id)
    {
        $customer = null;
        try {
            $customer = Customer::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['customer' => $customer], $status);
    }
}
