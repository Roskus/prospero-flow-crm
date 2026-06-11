<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Company;

use App\Models\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class CompanyReadController
{
    #[OAT\Get(
        path: '/company/{id}',
        summary: 'Get Company information',
        security: [['bearerAuth' => []]],
        tags: ['Company'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the Company', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Company found', content: new OAT\JsonContent(ref: '#/components/schemas/Company')),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Company not found'),
        ]
    )]
    public function read(Request $request, int $id): JsonResponse
    {
        if (! Auth::user()->hasRole('SuperAdmin') && Auth::user()->company_id !== $id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $company = Company::where('id', $id)->firstOrFail();

            return response()->json($company, 200);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'Company not found'], 404);
        }
    }
}
