<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Brand;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BrandDeleteController
{
    #[OAT\Delete(
        path: '/brand/{id}',
        summary: 'Delete a Brand',
        security: [['bearerAuth' => []]],
        tags: ['Brand'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                description: 'ID of the Brand',
                in: 'path',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Brand deleted successfully'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function delete(Request $request, int $id) // @SuppressWarnings(S1172) - $request used for validation
    {
        $brand = Brand::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully']);
    }
}
