<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Brand;

use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class BrandUpdateController
{
    private BrandRepository $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    #[OAT\Put(
        path: '/brand/{id}',
        summary: 'Update Brand information',
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
            new OAT\Response(response: 400, description: 'Bad request'),
            new OAT\Response(response: 404, description: 'Brand not found'),
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'max:80'],
        ]);

        $exists = Brand::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->exists();

        if (! $exists) {
            return response()->json(['message' => 'Brand not found'], Response::HTTP_NOT_FOUND);
        }

        $brand = $this->brandRepository->save($request->only(['name']) + ['id' => $id]);

        return response()->json(['brand' => $brand], Response::HTTP_OK);
    }
}
