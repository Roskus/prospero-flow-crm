<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Calendar;

use App\Repositories\CalendarRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'is_all_day' => ['boolean'],
            'description' => ['nullable', 'string'],
            'meeting' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'guest_list' => ['nullable', 'array'],
        ]);

        $event = $this->calendarRepository->save($request->all());

        return response()->json(['event' => ['id' => $event->id]], 201);
    }
}
