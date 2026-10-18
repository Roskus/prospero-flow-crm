<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ends the session (web) or rejects the token (API) of a user deactivated while logged in.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/*')) {
            $user = Auth::guard('api')->user();

            if ($user !== null && ! $user->active) {
                Auth::guard('api')->logout();

                return response()->json(['error' => 'Inactive user'], Response::HTTP_FORBIDDEN);
            }

            return $next($request);
        }

        $user = Auth::guard('web')->user();

        if ($user !== null && ! $user->active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => __('Your account is inactive.')]);
        }

        return $next($request);
    }
}
