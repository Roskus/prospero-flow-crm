<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SecurityLogger
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function log(string $event, array $context = []): void
    {
        Log::channel('security')->warning('security incident', array_merge([
            'event' => $event,
            'channel' => 'security',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'method' => request()->method(),
            'url' => request()->fullUrl(),
        ], $context));
    }
}
