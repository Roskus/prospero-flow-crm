<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Brand;

use App\Repositories\BrandRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandCreateController
{
    private BrandRepository $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * @OA\Post(
     *     path="/brand",
     *     summary="Create a Brand",
     *     tags={"Brand"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Brand found",
     *         @OA\JsonContent(ref="#/components/schemas/Brand")
     *     ),
     *     @OA\Response(response="404", description="Brand not found")
     * )
     */
    public function create(Request $request): JsonResponse
    {
        $status = 404;
        $valid = $request->validate([
            'name' => ['required', 'max:80'],
        ]);

        if ($valid) {
            $data = $request->all();
            $brand = $this->brandRepository->save($data);
            $response['brand'] = $brand;
            if ($brand) {
                $status = 201;
            }
        } else {
            $response = $valid->errors();
        }

        return response()->json($response, $status);
    }
}
