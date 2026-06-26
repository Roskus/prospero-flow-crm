<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Email;

use App\Http\Requests\EmailDeleteRequest;
use App\Models\Email;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class EmailDeleteController
{
    #[OAT\Delete(
        path: '/email/{id}',
        summary: 'Delete an Email',
        security: [['bearerAuth' => []]],
        tags: ['Email'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'ID of the Email', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'Email deleted successfully'),
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
        ]
    )]
    public function delete(EmailDeleteRequest $request, int $id)
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
