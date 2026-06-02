<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDeleteController
{
    /**
     * @OA\Delete (
     *      path="/customer/{id}",
     *      summary="Delete a Customer",
     *      tags={"Customer"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Customer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Customer deleted successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * Delete a customer by ID.
     *
     * @authenticated
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        $customer = Customer::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
