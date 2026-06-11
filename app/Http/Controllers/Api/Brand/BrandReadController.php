<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Brand;

use App\Models\Brand;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BrandReadController
{
    #[OAT\Get(
        path: '/brand/{id}',
        summary: 'Get Brand information',
        security: [['bearerAuth' => []]],
        tags: ['Brand'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of Brand',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Brand found',
                content: new OAT\JsonContent(ref: '#/components/schemas/Brand')
            ),
            new OAT\Response(response: 404, description: 'Brand not found'),
        ]
    )]
    public function read(int $id): JsonResponse
    {
        $brand = null;
        try {
            $brand = Brand::where('company_id', Auth::user()->company_id)->where('id', $id)->firstOrFail();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['brand' => $brand], $status);
    }
}
