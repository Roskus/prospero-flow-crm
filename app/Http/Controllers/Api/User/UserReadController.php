<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OAT;

class UserReadController
{
    #[OAT\Get(
        path: '/user/{id}',
        summary: 'Get User information',
        security: [['bearerAuth' => []]],
        tags: ['User'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of User', schema: new OAT\Schema(type: 'integer')),
        ],
        responses: [
            new OAT\Response(response: 200, description: 'User found', content: new OAT\JsonContent(ref: '#/components/schemas/User')),
            new OAT\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function read(int $id): JsonResponse
    {
        $user = null;
        try {
            $user = User::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
            $status = 200;
        } catch (ModelNotFoundException $e) {
            $status = 404;
        }

        return response()->json(['user' => $user], $status);
    }
}
