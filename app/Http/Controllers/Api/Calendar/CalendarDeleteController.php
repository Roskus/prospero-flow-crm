<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Calendar;

use App\Http\Requests\CalendarDeleteRequest;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class CalendarDeleteController
{
    #[OAT\Delete(
        path: '/calendar/{id}',
        summary: 'Delete a Calendar event',
        security: [['bearerAuth' => []]],
        tags: ['Calendar'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the Calendar event', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Calendar event deleted successfully'),
            new OAT\Response(response: 404, description: 'Calendar event not found'),
        ]
    )]
    public function delete(CalendarDeleteRequest $request, int $id): JsonResponse
    {
        $event = Calendar::where('company_id', Auth::user()->company_id)->find($id);

        if (! $event) {
            return response()->json(['message' => 'Calendar event not found'], 404);
        }

        $event->delete();

        return response()->json(['message' => 'Calendar event deleted successfully']);
    }
}
