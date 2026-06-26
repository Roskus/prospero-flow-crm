<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class HandleApiValidationErrors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->exception instanceof ValidationException && $request->expectsJson()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Validation errors',
                'data' => $response->exception->errors(),
            ], 422);
        }

        return $response;
    }
}
