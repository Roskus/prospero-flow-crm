<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class ProductDeleteController
{
    #[OAT\Delete(
        path: '/product/{id}',
        summary: 'Delete a Product',
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
            new OAT\Response(response: 200, description: 'Product deleted successfully'),
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
        ]
    )]
    public function delete(Request $request, int $id): JsonResponse
    {
        $product = Product::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
