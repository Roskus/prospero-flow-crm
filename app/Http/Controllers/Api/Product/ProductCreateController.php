<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ProductCreateController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @OA\Post(
     *     path="/product",
     *     summary="Create a Product",
     *     tags={"Product"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(response="201", description="Product created successfully"),
     *     @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     */
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
