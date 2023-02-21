<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Customer;

use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CustomerUpdateController
{
    private CustomerRepository $customerSaveRepository;

    public function __construct(CustomerRepository $customerSaveRepository)
    {
        $this->customerSaveRepository = $customerSaveRepository;
    }

    /**
     * @OA\Put(
     *     path="/customer/{id}",
     *     summary="Update a Customer",
     *     tags={"Customer"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Lead",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(response="200", description="Customer updated successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters"),
     *     @OA\Response(response="404", description="Customer not found")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
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
            $params['id'] = $id;
            $params = array_merge($params, $request->all());
            $customer = $this->customerSaveRepository->save($params);
            if ($customer) {
                $status = 200;
                $data['customer'] = $customer->toArray();
            }
        }

        return response()->json($data, $status);
    }
}
