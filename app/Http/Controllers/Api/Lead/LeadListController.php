<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Http\Controllers\Api\ApiController;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            new OAT\Parameter(name: 'per_page', description: 'Number of results per page, at most '.self::MAX_ITEMS_PER_PAGE, in: 'query', required: false, schema: new OAT\Schema(type: 'integer', default: self::DEFAULT_ITEMS_PER_PAGE, maximum: self::MAX_ITEMS_PER_PAGE, minimum: 1)),
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
    public function index(Request $request): JsonResponse
    {
        $leads = Lead::where('company_id', Auth::user()->company_id)
            ->orderByDesc('id')
            ->paginate($this->itemsPerPage($request));

        return $this->paginatedListResponse('leads', $leads);
    }
}
