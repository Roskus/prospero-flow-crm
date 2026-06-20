<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LimitContentLength
{
    private const int MAX_CONTENT_LENGTH = 50 * 1024 * 1024; // 50MB

    public function handle(Request $request, Closure $next): Response
    {
        $contentLength = $request->server('CONTENT_LENGTH');

        if ($contentLength && (int) $contentLength > self::MAX_CONTENT_LENGTH) {
            return response()->json([
                'message' => 'Request body too large. Maximum size is 50MB.',
            ], 413);
        }

        return $next($request);
    }
}
