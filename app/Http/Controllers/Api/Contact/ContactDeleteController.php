<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for contact deletion.
 *
 * @group Contacts
 */
class ContactDeleteController
{
    /**
     * @OA\Delete (
     *      path="/contact/{id}",
     *      summary="Delete a Contact",
     *      tags={"Contact"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the Contact",
     *         required=true,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Contact deleted successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * Delete a contact by ID.
     *
     * @authenticated
     *
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        $contact = Contact::find($id)->where('user_id', Auth::id())->get();
        $contact->delete();

        return response()->json(['message' => 'Contact deleted successfully']);
    }
}
