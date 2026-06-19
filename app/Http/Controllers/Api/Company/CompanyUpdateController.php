<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Http\Requests\CompanyUpdateRequest;
use App\Repositories\CompanyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class CompanyUpdateController
{
    public function __construct(private CompanyRepository $companyRepository) {}

    #[OAT\Put(
        path: '/company/{id}',
        summary: 'Update a Company',
        security: [['bearerAuth' => []]],
        tags: ['Company'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of Company', schema: new OAT\Schema(type: 'integer')),
        ],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Company')
        ),
        responses: [
            new OAT\Response(response: 200, description: 'Company updated successfully'),
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Company not found'),
        ]
    )]
    public function update(CompanyUpdateRequest $request, int $id): JsonResponse
    {
        if (Auth::user()->company_id !== $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $params = array_merge(['id' => $id], $request->all());
        $company = $this->companyRepository->save($params);

        return response()->json($company, $company ? 200 : 400);
    }
}
