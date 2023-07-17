<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ticket;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for ticket list.
 *
 * @group Tickets
 */
class TicketListController
{
    /**
     * @OA\Get(
     *     path="/ticket",
     *     summary="Ticket list by company",
     *     tags={"Ticket"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Ticket list retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     )
     * )
     *
     * Get the list of tickets by company.
     *
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $count = Ticket::where('company_id', Auth::user()->company_id)->count();
        $tickets = Ticket::where('company_id', Auth::user()->company_id)->get();

        return response()->json(['count' => $count, 'tickets' => $tickets]);
    }
}
