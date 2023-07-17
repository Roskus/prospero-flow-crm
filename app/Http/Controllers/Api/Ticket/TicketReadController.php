<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ticket;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for ticket read.
 *
 * @group Tickets
 */
class TicketReadController
{
    /**
     * @OA\Get(
     *     path="/ticket/{id}",
     *     summary="Get Ticket information",
     *     tags={"Ticket"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Ticket",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Ticket found",
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     ),
     *     @OA\Response(response="404", description="Ticket not found")
     * )
     *
     * Get the information of a ticket by ID.
     *
     * @authenticated
     *
     * @param  int  $id
     * @return JsonResponse
     */
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
