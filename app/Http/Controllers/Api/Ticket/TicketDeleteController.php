<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ticket;

use App\Http\Requests\TicketDeleteRequest;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class TicketDeleteController
{
    #[OAT\Delete(
        path: '/ticket/{id}',
        summary: 'Delete a Ticket',
        security: [['bearerAuth' => []]],
        tags: ['Ticket'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'ID of the Ticket', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Ticket deleted successfully'),
            new OAT\Response(response: 401, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Ticket not found'),
        ]
    )]
    public function delete(TicketDeleteRequest $request, int $id): JsonResponse
    {
        $ticket = Ticket::where('company_id', (int) Auth::user()->company_id)->where('id', $id)->first();
        if (! $ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        if ($ticket->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully']);
    }
}
