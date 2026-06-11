<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class LeadReadController
{
    #[OAT\Get(
        path: '/lead/{id}',
        summary: 'Get Lead information',
        security: [['bearerAuth' => []]],
        tags: ['Lead'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of Lead', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Lead found', content: new OAT\JsonContent(ref: '#/components/schemas/Lead')),
            new OAT\Response(response: 404, description: 'Lead not found'),
        ]
    )]
    public function read(int $id): JsonResponse
    {
        $lead = null;
        try {
            $lead = Lead::where('company_id', Auth::user()->company_id)->where('id', $id)->firstOrFail();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['lead' => $lead], $status);
    }
}
