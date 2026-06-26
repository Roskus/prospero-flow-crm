<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class UserDeleteController
{
    #[OAT\Delete(
        path: '/user/{id}',
        summary: 'Delete a User',
        security: [['bearerAuth' => []]],
        tags: ['User'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'ID of the User', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'User deleted successfully'),
            new OAT\Response(response: 400, description: 'Bad request, please review the parameters'),
        ]
    )]
    public function delete(Request $request, int $id) // @SuppressWarnings(S1172) - $request used for validation
    {
        $user = User::find($id);
        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Ensure the user can only delete users from their own company, except for users from company with ID 1
        if ($user->company_id !== (int) Auth::user()->company_id && (int) Auth::user()->company_id !== Company::DEFAULT_COMPANY) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
