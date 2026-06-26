<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Http\Requests\CompanyDeleteRequest;
use App\Models\Company;
use OpenApi\Attributes as OAT;

class CompanyDeleteController
{
    #[OAT\Delete(
        path: '/company/{id}',
        summary: 'Delete a Company',
        security: [['bearerAuth' => []]],
        tags: ['Company'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'ID of the Company', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Company deleted successfully'),
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
        ]
    )]
    public function delete(CompanyDeleteRequest $request, int $id) // @SuppressWarnings(S1172) - $request used for validation
    {
        $company = Company::find($id);
        if (! $company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }
}
