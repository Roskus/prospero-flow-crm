<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\UserDeleteRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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
            new OAT\Response(response: 403, description: 'Forbidden'),
            new OAT\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function delete(UserDeleteRequest $request, int $id): JsonResponse
    {
        $user = User::query()
            ->unless(Auth::user()->hasRole('SuperAdmin'), fn ($query) => $query->where('company_id', Auth::user()->company_id))
            ->find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->hasRole('SuperAdmin') && ! Auth::user()->hasRole('SuperAdmin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
