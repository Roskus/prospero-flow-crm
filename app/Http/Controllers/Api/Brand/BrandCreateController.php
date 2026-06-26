<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Brand;

use App\Repositories\BrandRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OAT;

class BrandCreateController
{
    private BrandRepository $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    #[OAT\Post(
        path: '/brand',
        summary: 'Create a Brand',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Brand data',
            required: true,
            content: new OAT\JsonContent(
                required: ['name'],
                properties: [
                    new OAT\Property(property: 'name', type: 'string', maxLength: 80, example: 'Example Brand'),
                ]
            )
        ),
        tags: ['Brand'],
        responses: [
            new OAT\Response(
                response: 201,
                description: 'Brand created successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Brand')
            ),
            new OAT\Response(response: 400, description: 'Invalid input data'),
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $status = 404;
        $valid = $request->validate([
            'name' => ['required', 'max:80'],
        ]);

        if ($valid) {
            $data = $request->all();
            $brand = $this->brandRepository->save($data);
            $response['brand'] = $brand;
            if ($brand) {
                $status = 201;
            }
        } else {
            $response = $valid->errors();
        }

        return response()->json($response, $status);
    }
}
