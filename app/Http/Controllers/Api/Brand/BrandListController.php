<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Brand;

use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BrandListController
{
    #[OAT\Get(
        path: '/brand',
        summary: 'Brand list by company',
        security: [['bearerAuth' => []]],
        tags: ['Brand'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Brand list retrieved successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Brand')
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $count = Brand::where('company_id', Auth::user()->company_id)->count();
        $brands = Brand::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'brands' => $brands]);
    }
}
