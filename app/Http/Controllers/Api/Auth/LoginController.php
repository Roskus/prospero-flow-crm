<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OAT;

class LoginController extends Controller
{
    #[OAT\Post(
        path: '/auth/login',
        summary: 'Login',
        tags: ['Auth'],
        requestBody: new OAT\RequestBody(
            required: true,
            description: 'Pass user credentials',
            content: new OAT\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OAT\Property(property: 'email', type: 'string', format: 'email', example: 'admin@admin.com'),
                    new OAT\Property(property: 'password', type: 'string', format: 'password', example: 'admin'),
                ],
            ),
        ),
        responses: [
            new OAT\Response(response: 200, description: 'Success'),
            new OAT\Response(response: 401, description: 'Unauthorized or wrong credentials'),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = request(['email', 'password']);
        $token = auth('api')->attempt($credentials);

        if (empty($token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 120,
        ]);
    }
}
