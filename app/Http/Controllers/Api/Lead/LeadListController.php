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
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Lead list retrieved successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Lead')
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $count = Lead::where('company_id', Auth::user()->company_id)->count();
        $leads = Lead::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'leads' => $leads]);
    }
}
