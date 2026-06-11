<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
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
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
        ]
    )]
    public function create(Request $request)
    {
        $status = 400;
        $data = [];
        $valid = $request->validate([
            'name' => ['required', 'max:80'],
        ]);
        $params = $request->all();

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
