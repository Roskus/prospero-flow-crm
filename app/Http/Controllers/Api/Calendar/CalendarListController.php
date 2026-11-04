<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Calendar;

use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class CalendarListController
{
    #[OAT\Get(
        path: '/calendar',
        summary: 'Calendar events list by company',
        security: [['bearerAuth' => []]],
        tags: ['Calendar'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Calendar events retrieved successfully',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'count', description: 'Total number of results', type: 'integer', example: 1),
                        new OAT\Property(property: 'events', type: 'array', items: new OAT\Items(ref: '#/components/schemas/Calendar')),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $events = Calendar::where('company_id', Auth::user()->company_id)
            ->with('organizer')
            ->get();

        return response()->json(['count' => $events->count(), 'events' => $events]);
    }
}
