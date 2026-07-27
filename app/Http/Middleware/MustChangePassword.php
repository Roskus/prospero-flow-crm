<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->must_change_password && ! $request->routeIs('password.change.form') && ! $request->routeIs('password.change.update')) {
            return redirect()->route('password.change.form');
        }

        return $next($request);
    }
}
