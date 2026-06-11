<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ticket;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class TicketReadController
{
    #[OAT\Get(
        path: '/ticket/{id}',
        summary: 'Get Ticket information',
        security: [['bearerAuth' => []]],
        tags: ['Ticket'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'ID of the Ticket', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Ticket found', content: new OAT\JsonContent(ref: '#/components/schemas/Ticket')),
            new OAT\Response(response: 404, description: 'Ticket not found'),
        ]
    )]
    public function read(int $id): JsonResponse
    {
        $ticket = null;
        try {
            $ticket = Ticket::where('company_id', Auth::user()->company_id)->where('id', $id)->firstOrFail();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['ticket' => $ticket], $status);
    }
}
