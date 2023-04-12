<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CustomerCreateController
{
    private CustomerRepository $customerSaveRepository;

    public function __construct(CustomerRepository $customerSaveRepository)
    {
        $this->customerSaveRepository = $customerSaveRepository;
    }

    /**
     * @OA\Post(
     *     path="/customer",
     *     summary="Create a Customer",
     *     tags={"Customer"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(response="201", description="Customer created successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function create(Request $request): JsonResponse
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'max:254'],
            'phone' => ['required', 'max:15'],
            'country_id' => ['required', 'max:2'],
        ]);

        if ($valid) {
            $customer = $this->customerSaveRepository->save($request->all());
            if ($customer) {
                $status = 201;
                $data['customer'] = ['id' => $customer->id];
            }
        }

        return response()->json($data, $status);
    }
}
