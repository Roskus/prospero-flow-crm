<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class ProductReadController
{
    #[OAT\Get(
        path: '/product/{id}',
        summary: 'Get Product information',
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
            new OAT\Response(
                response: 200,
                description: 'Product found',
                content: new OAT\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OAT\Response(response: 404, description: 'Product not found'),
        ]
    )]
    public function read(Request $request, int $id)
    {
        $product = null;
        try {
            $product = Product::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['product' => $product], $status);
    }
}
