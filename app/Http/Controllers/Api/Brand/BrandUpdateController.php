<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Brand;

use App\Repositories\BrandRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandUpdateController
{
    private BrandRepository $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * @OA\Put(
     *     path="/brand/{id}",
     *     summary="Update Brand information",
     *     tags={"Brand"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of Brand",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Brand found",
     *         @OA\JsonContent(ref="#/components/schemas/Brand")
     *     ),
     *     @OA\Response(response="404", description="Brand not found")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $status = 404;
        $valid = $request->validate([
            'name' => ['required', 'max:80'],
        ]);

        if ($valid) {
            $data = $request->all();
            $data['id'] = $id;
            $brand = $this->brandRepository->save($data);
            $response['brand'] = $brand;
        } else {
            $response = $valid->errors();
        }

        if ($brand) {
            $status = 200;
        }

        return response()->json($response, $status);
    }
}
