<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Http\Requests\ProductUpdateRequest;
use App\Repositories\ProductRepository;
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
                in: 'path',
                description: 'Id of Product',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Product updated successfully'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function update(ProductUpdateRequest $request, int $id)
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
