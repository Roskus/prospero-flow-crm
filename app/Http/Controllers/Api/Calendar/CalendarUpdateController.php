<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Calendar;

use App\Models\Calendar;
use App\Repositories\CalendarRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class CalendarUpdateController
{
    public function __construct(private CalendarRepository $calendarRepository) {}

    #[OAT\Put(
        path: '/calendar/{id}',
        summary: 'Update a Calendar event',
        security: [['bearerAuth' => []]],
        tags: ['Calendar'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of the Calendar event', schema: new OAT\Schema(type: 'integer')),
        ],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Calendar')
        ),
        responses: [
            new OAT\Response(response: 200, description: 'Calendar event updated successfully', content: new OAT\JsonContent(ref: '#/components/schemas/Calendar')),
            new OAT\Response(response: 404, description: 'Calendar event not found'),
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $event = Calendar::where('company_id', Auth::user()->company_id)->find($id);

        if (! $event) {
            return response()->json(['message' => 'Calendar event not found'], 404);
        }

        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'date' => ['sometimes', 'date'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i'],
            'is_all_day' => ['boolean'],
            'description' => ['nullable', 'string'],
            'meeting' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'guest_list' => ['nullable', 'array'],
        ]);

        $data = array_merge($request->all(), ['id' => $id]);
        $event = $this->calendarRepository->save($data);

        return response()->json(['event' => $event]);
    }
}
