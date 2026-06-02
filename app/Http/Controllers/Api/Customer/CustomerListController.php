<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\ApiController;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerListController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/customer",
     *     summary="Customer list by company",
     *     tags={"Customer"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Customers list retrived successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $customers = Customer::where('company_id', $this->user->company_id)
            ->paginate($request->integer('per_page', self::DEFAULT_ITEMS_PER_PAGE));

        return response()->json($customers);
    }
}
