<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ticket;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class TicketListController
{
    #[OAT\Get(
        path: '/ticket',
        summary: 'Ticket list by company',
        security: [['bearerAuth' => []]],
        tags: ['Ticket'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Ticket list retrieved successfully',
                content: new OAT\JsonContent(ref: '#/components/schemas/Ticket')
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $count = Ticket::where('company_id', Auth::user()->company_id)->count();
        $tickets = Ticket::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'tickets' => $tickets]);
    }
}
