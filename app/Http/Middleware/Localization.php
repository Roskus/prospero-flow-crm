<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = null;

        if (Auth::check()) {
            $locale = Auth::user()->lang;
        }

        if (session()->has('locale')) {
            $locale = session()->get('locale');
        }

        if (! $locale) {
            $locales = array_keys(config('app.locales'));
            $locale = $request->getPreferredLanguage($locales);

            if ($locale && ! in_array($locale, $locales)) {
                $base = substr($locale, 0, 2);
                $locale = in_array($base, $locales) ? $base : null;
            }
        }

        if ($locale) {
            App::setlocale($locale);
        }

        return $next($request);
    }
}
