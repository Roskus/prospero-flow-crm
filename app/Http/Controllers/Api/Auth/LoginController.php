<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\SecurityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OAT;

class LoginController extends Controller
{
    private const int SECONDS_PER_MINUTE = 60;

    public function __construct(private SecurityLogger $securityLogger) {}

    #[OAT\Post(
        path: '/auth/login',
        summary: 'Login',
        requestBody: new OAT\RequestBody(
            description: 'Pass user credentials',
            required: true,
            content: new OAT\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OAT\Property(property: 'email', type: 'string', format: 'email', example: 'admin@admin.com'),
                    new OAT\Property(property: 'password', type: 'string', format: 'password', example: 'admin'),
                ],
            ),
        ),
        tags: ['Auth'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Success',
                content: new OAT\JsonContent(
                    properties: [
                        new OAT\Property(property: 'access_token', description: 'JWT to send as `Authorization: Bearer <token>`', type: 'string'),
                        new OAT\Property(property: 'token_type', type: 'string', example: 'bearer'),
                        new OAT\Property(property: 'expires_in', description: 'Token lifetime in seconds (JWT_TTL minutes x 60)', type: 'integer', example: 3600),
                    ],
                    type: 'object'
                )
            ),
            new OAT\Response(response: 401, description: 'Unauthorized or wrong credentials'),
            new OAT\Response(response: 429, description: 'Too many login attempts'),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = request(['email', 'password']);
        $token = auth('api')->attempt(array_merge($credentials, ['active' => true]));

        if (empty($token)) {
            $this->securityLogger->log('api_login_failed', [
                'attempted_email' => $credentials['email'],
            ]);

            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User, with its `company_id` so API clients can read their company.
     */
    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user()->makeVisible('company_id'));
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
     * Token response of login and refresh. `expires_in` is the token lifetime in seconds: the
     * configured `jwt.ttl` (`JWT_TTL`, in minutes, 60 by default) converted to seconds.
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * self::SECONDS_PER_MINUTE,
        ]);
    }
}
