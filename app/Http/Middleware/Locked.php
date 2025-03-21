<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;

class Locked
{
    public function handle($request, Closure $next)
    {
        if ($request->is('unlock') || $request->is('lock')) {
            return $next($request);
        }

        if (session('locked', false) && $request->path() != 'lock') {
            return redirect('/lock');
        }

        return $next($request);
    }
}
