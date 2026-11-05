<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProspectListRequest;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class LeadListController extends ApiController
{
    #[OAT\Get(
        path: '/lead',
        summary: 'Lead list by company',
        security: [['bearerAuth' => []]],
        tags: ['Lead'],
        parameters: [
            new OAT\Parameter(ref: '#/components/parameters/Search'),
            new OAT\Parameter(ref: '#/components/parameters/Page'),
            new OAT\Parameter(ref: '#/components/parameters/PerPage'),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Lead list retrieved successfully',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'count', description: 'Total number of results', type: 'integer', example: 1),
                        new OAT\Property(property: 'leads', description: 'Results of the page', type: 'array', items: new OAT\Items(ref: '#/components/schemas/Lead')),
                    ],
                    type: 'object',
                    allOf: [new OAT\Schema(ref: '#/components/schemas/Pagination')]
                )
            ),
        ]
    )]
    public function index(ProspectListRequest $request): JsonResponse
    {
        $leads = Lead::where('company_id', Auth::user()->company_id)
            ->search($request->validated('search'))
            ->orderByDesc('id')
            ->paginate($this->itemsPerPage($request));

        return $this->paginatedListResponse('leads', $leads);
    }
}
