<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Http\Requests\CompanyCreateRequest;
use App\Repositories\CompanyRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class CompanyCreateController
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    #[OAT\Post(
        path: '/company',
        summary: 'Create a Company',
        security: [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Company')
        ),
        tags: ['Company'],
        responses: [
            new OAT\Response(response: 201, description: 'Company created successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized - SuperAdmin only'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function create(CompanyCreateRequest $request): JsonResponse
    {
        $company = $this->companyRepository->save($request->validated());

        return response()->json(['company' => $company], 201);
    }
}
