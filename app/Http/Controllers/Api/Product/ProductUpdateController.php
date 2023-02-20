<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ProductUpdateController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @OA\Put(
     *     path="/product/{id}",
     *     summary="Update a Product",
     *     tags={"Product"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of Product",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Product updated successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     */
    public function update(Request $request, int $id)
    {
        $status = 400;
        $data = [];
        $params['id'] = $id;
        $params = array_merge($params, $request->all());
        $valid = $request->validate([
            'name' => ['required', 'max:80'],
        ]);

        if ($valid) {
            $product = $this->productRepository->save($params);

            if ($product) {
                $status = 200;
                $data['product'] = $product->toArray();
            }
        }

        return response()->json($data, $status);
    }
}
