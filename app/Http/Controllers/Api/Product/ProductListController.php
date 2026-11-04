<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class ProductListController
{
    #[OAT\Get(
        path: '/product',
        summary: 'Products list by company',
        security: [['bearerAuth' => []]],
        tags: ['Product'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Products list retrieved successfully',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'count', description: 'Total number of results', type: 'integer', example: 1),
                        new OAT\Property(property: 'products', type: 'array', items: new OAT\Items(ref: '#/components/schemas/Product')),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function index(Request $request)
    {
        $count = Product::where('company_id', Auth::user()->company_id)->count();
        $products = Product::where('company_id', Auth::user()->company_id)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json(['count' => $count, 'products' => $products]);
    }
}
