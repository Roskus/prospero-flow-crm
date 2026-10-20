<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SecurityLogger;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;

class LoginController extends Controller implements HasMiddleware
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        sendFailedLoginResponse as protected sendTraitFailedLoginResponse;
        sendLockoutResponse as protected sendTraitLockoutResponse;
    }

    /**
     * Where to redirect users after login.
     */
    protected string $redirectTo = '/';

    public static function middleware(): array
    {
        return [
            new Middleware('guest', except: ['logout']),
        ];
    }

    public function __construct(private SecurityLogger $securityLogger) {}

    /**
     * Inactive users cannot log in.
     *
     * @return array<string, mixed>
     */
    protected function credentials(Request $request): array
    {
        return array_merge($request->only($this->username(), 'password'), ['active' => true]);
    }

    protected function sendFailedLoginResponse(Request $request): void
    {
        $this->securityLogger->log('web_login_failed', [
            'attempted_email' => (string) $request->input($this->username()),
        ]);

        $this->sendTraitFailedLoginResponse($request);
    }

    protected function sendLockoutResponse(Request $request): void
    {
        $this->securityLogger->log('web_login_throttled', [
            'attempted_email' => (string) $request->input($this->username()),
        ]);

        $this->sendTraitLockoutResponse($request);
    }

    /**
     * Every web session starts in the user's own company, never silently inside a client company.
     */
    protected function authenticated(Request $request, $user): void
    {
        $user->company_id = $user->defaultCompanyId();
        $user->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp(),
        ]);
    }
}
