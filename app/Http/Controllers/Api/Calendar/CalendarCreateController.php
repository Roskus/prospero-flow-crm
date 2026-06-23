<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Calendar;

use App\Http\Requests\CalendarCreateRequest;
use App\Repositories\CalendarRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OAT;

class CalendarCreateController
{
    public function __construct(private CalendarRepository $calendarRepository) {}

    #[OAT\Post(
        path: '/calendar',
        summary: 'Create a Calendar event',
        security: [['bearerAuth' => []]],
        tags: ['Calendar'],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Calendar')
        ),
        responses: [
            new OAT\Response(response: 201, description: 'Calendar event created successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function create(CalendarCreateRequest $request): JsonResponse
    {
        $event = $this->calendarRepository->save($request->validated());

        return response()->json(['event' => ['id' => $event->id]], 201);
    }
}
