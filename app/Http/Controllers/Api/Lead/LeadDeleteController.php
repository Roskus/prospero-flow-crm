<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Lead;

use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class LeadDeleteController
{
    #[OAT\Delete(
        path: '/lead/{id}',
        summary: 'Delete a Lead',
        security: [['bearerAuth' => []]],
        tags: ['Lead'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'ID of the Lead', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Lead deleted successfully'),
            new OAT\Response(response: 404, description: 'Lead not found'),
        ]
    )]
    public function delete(Request $request, int $id): JsonResponse
    {
        $lead = Lead::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        $lead->delete();

        return response()->json(['message' => 'Lead deleted successfully']);
    }
}
