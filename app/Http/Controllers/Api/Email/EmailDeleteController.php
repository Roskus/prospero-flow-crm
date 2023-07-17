<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Email;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for email deletion.
 *
 * @group Emails
 */
class EmailDeleteController
{
    /**
     * @OA\Delete (
     *      path="/email/{id}",
     *      summary="Delete an Email",
     *      tags={"Email"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Email",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Email deleted successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * Delete an email by ID.
     *
     * @authenticated
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        $email = Email::find($id);
        if (! $email) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        // Ensure the user can only delete their own emails
        if ($email->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $email->delete();

        return response()->json(['message' => 'Email deleted successfully']);
    }
}
