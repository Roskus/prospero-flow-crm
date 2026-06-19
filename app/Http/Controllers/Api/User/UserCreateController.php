<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\UserCreateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OAT;

class UserCreateController
{
    #[OAT\Post(
        path: '/user',
        summary: 'Create a User',
        security: [['bearerAuth' => []]],
        tags: ['User'],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/User')
        ),
        responses: [
            new OAT\Response(response: 201, description: 'User created successfully'),
            new OAT\Response(response: 403, description: 'Unauthorized - SuperAdmin only'),
            new OAT\Response(response: 422, description: 'Validation failed'),
        ]
    )]
    public function create(UserCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json(['user' => $user], 201);
    }
}
