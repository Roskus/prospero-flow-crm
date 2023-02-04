<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class UserDeleteController
{
    #[OAT\Delete(
        path: '/user/{id}',
        summary: 'Delete an user',
        security: [['bearerAuth']],
        tags: ['User'],
        parameters: [new OAT\Parameter(name: 'id', in: 'path', required: true, schema: new OAT\Schema(type: 'int'))],
        responses: [
            new OAT\Response(response: 200, description: 'User deleted successfully'),
            new OAT\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function delete(int $id): \Illuminate\Http\JsonResponse
    {
        $status = 200;
        try {
            $user = User::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
            $user->delete();
        } catch(ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['message' => ''], $status);
    }
}
