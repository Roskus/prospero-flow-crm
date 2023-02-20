<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerListController
{
    /**
     * @OA\Get(
     *     path="/customer",
     *     summary="Customer list by company",
     *     tags={"Customer"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Customers list retrived successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     )
     * )
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $count = Customer::where('company_id', Auth::user()->company_id)->count();
        $customers = Customer::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'customers' => $customers]);
    }
}
