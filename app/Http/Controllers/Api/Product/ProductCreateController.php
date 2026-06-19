<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class ProductCreateController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[OAT\Post(
        path: '/product',
        summary: 'Create a Product',
        security: [['bearerAuth' => []]],
        tags: ['Product'],
        responses: [
            new OAT\Response(response: 201, description: 'Product created successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
        ]
    )]
    public function create(ProductRequest $request): JsonResponse
    {
        $product = $this->productRepository->save($request->validated());

        return response()->json(['product' => $product->toArray()], 201);
    }
}
