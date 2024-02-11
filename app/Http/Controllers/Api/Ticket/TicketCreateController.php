<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Ticket;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TicketCreateController
{
    /**
     * @OA\Post(
     *     path="/ticket",
     *     summary="Create Support Ticket",
     *     tags={"Ticket"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(
     *         response="201",
     *         description="Support Ticket created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Ticket")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     *
     * Create a Support Ticket
     *
     * @authenticated
     */
    public function create(Request $request)
    {
        // Validate the request data
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

        // Create the ticket
        $ticket = Ticket::create($request->all());

        return response()->json(['ticket' => $ticket], Response::HTTP_CREATED);
    }
}
