<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Models\Contact;
use OpenApi\Annotations as OA;

class ContactReadController
{
    /**
     * @OA\Get(
     *     path="/contact/{id}",
     *     summary="Get a contact",
     *     tags={"Contact"},
     *     security={{"bearerAuth": {} }},
     *     @OA\Response(response="404", description="Contact not found"),
     *     @OA\Response(response="200", description="Contact retrived successfully")
     * )
     */
    public function read($id)
    {
        $contact = Contact::find($id);
        return response()->json(['contact' => $contact], ($contact) ? 200 : 404);
    }
}
