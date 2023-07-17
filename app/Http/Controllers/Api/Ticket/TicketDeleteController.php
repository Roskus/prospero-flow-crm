<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ticket;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for ticket deletion.
 *
 * @group Tickets
 */
class TicketDeleteController
{
    /**
     * @OA\Delete (
     *      path="/ticket/{id}",
     *      summary="Delete a Ticket",
     *      tags={"Ticket"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Ticket",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Ticket deleted successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * Delete a ticket by ID.
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        $ticket = Ticket::find($id);
        if (! $ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        // Ensure the user can only delete their own tickets
        if ($ticket->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully']);
    }
}
