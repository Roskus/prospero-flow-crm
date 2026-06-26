<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Calendar;

use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class CalendarReadController
{
    #[OAT\Get(
        path: '/calendar/{id}',
        summary: 'Get Calendar event information',
        security: [['bearerAuth' => []]],
        tags: ['Calendar'],
        parameters: [
            new OAT\Parameter(name: 'id', description: 'Id of the Calendar event', in: 'path', required: true, schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Calendar event found', content: new OAT\JsonContent(ref: '#/components/schemas/Calendar')),
            new OAT\Response(response: 404, description: 'Calendar event not found'),
        ]
    )]
    public function read(int $id): JsonResponse
    {
        $event = Calendar::where('company_id', Auth::user()->company_id)
            ->with('organizer')
            ->find($id);

        if (! $event) {
            return response()->json(['message' => 'Calendar event not found'], 404);
        }

        return response()->json(['event' => $event]);
    }
}
