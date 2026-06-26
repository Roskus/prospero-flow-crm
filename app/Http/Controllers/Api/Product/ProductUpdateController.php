<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Http\Requests\ProductUpdateRequest;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class ProductUpdateController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[OAT\Put(
        path: '/product/{id}',
        summary: 'Update a Product',
        security: [['bearerAuth' => []]],
        tags: ['Product'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                description: 'Id of Product',
                in: 'path',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Product updated successfully'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function update(ProductUpdateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $data['id'] = $id;
        $product = $this->productRepository->save($data);

        return response()->json(['product' => $product->toArray()], 200);
    }
}
