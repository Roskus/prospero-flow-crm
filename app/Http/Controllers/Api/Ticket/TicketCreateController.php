<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ticket;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OAT;

class TicketCreateController
{
    #[OAT\Post(
        path: '/ticket',
        summary: 'Create Support Ticket',
        security: [['bearerAuth' => []]],
        tags: ['Ticket'],
        responses: [
            new OAT\Response(
                response: 201,
                description: 'Support Ticket created successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Ticket')
            ),
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'customer_id' => 'required|integer',
            'type' => 'required|string',
            'priority' => 'required|string',
            'order_id' => 'required|integer',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $ticket = Ticket::create(array_merge($request->all(), [
            'company_id' => Auth::user()->company_id,
            'created_by' => Auth::id(),
        ]));

        return response()->json(['ticket' => $ticket], Response::HTTP_CREATED);
    }
}
