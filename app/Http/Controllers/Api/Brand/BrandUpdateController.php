<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Brand;

use App\Repositories\BrandRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     *     @OA\Response(response="400", description="Bad request"),
     *     @OA\Response(response="404", description="Brand not found"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'max:80'],
        ]);

        $brand = $this->brandRepository->save($request->only(['name']) + ['id' => $id]);

        if ($brand) {
            return response()->json(['brand' => $brand], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
