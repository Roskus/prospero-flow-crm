<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OAT;

class UserUpdateController
{
    #[OAT\Put(
        path: '/user/{id}',
        summary: 'Update User information',
        security: [['bearerAuth' => []]],
        tags: ['User'],
        parameters: [
            new OAT\Parameter(name: 'id', in: 'path', required: true, description: 'Id of User', schema: new OAT\Schema(type: 'integer')),
        ],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/User')
        ),
        responses: [
            new OAT\Response(response: 200, description: 'User updated successfully', content: new OAT\JsonContent(ref: '#/components/schemas/User')),
            new OAT\Response(response: 404, description: 'User not found'),
            new OAT\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $rules = [
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:15',
            'photo' => 'nullable|string|max:255',
            'lang' => 'required|string|max:2',
            'timezone' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
        ];

        $validatedData = Validator::make($request->all(), $rules)->validate();

        $user = User::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->first();

        if (! $user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if (! empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json(['user' => $user], Response::HTTP_OK);
    }
}
