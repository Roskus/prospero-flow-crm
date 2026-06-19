<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Contact;

use App\Http\Requests\ContactDeleteRequest;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class ContactDeleteController
{
    #[OAT\Delete(
        path: '/contact/{id}',
        summary: 'Delete a Contact',
        security: [['bearerAuth' => []]],
        tags: ['Contact'],
        parameters: [
            new OAT\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of the Contact',
                required: true,
                schema: new OAT\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Contact deleted successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized'),
            new OAT\Response(response: 404, description: 'Contact not found'),
        ]
    )]
    public function delete(ContactDeleteRequest $request, int $id): JsonResponse
    {
        $contact = Contact::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        $contact->delete();

        return response()->json(['message' => 'Contact deleted successfully']);
    }
}
