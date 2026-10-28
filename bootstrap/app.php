<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\LimitContentLength;
use App\Http\Middleware\Localization;
use App\Http\Middleware\Locked;
use App\Http\Middleware\MustChangePassword;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(LimitContentLength::class);

        $middleware->throttleApi();

        $middleware->web(append: [
            EnsureUserIsActive::class,
            Localization::class,
            MustChangePassword::class,
        ]);

        $middleware->api(append: [
            EnsureUserIsActive::class,
        ]);

        $middleware->alias([
            'locked' => Locked::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('crm:contact:email-predict')->hourly();
        $schedule->command('crm:email:validate')->daily();
        $schedule->command('crm:phone:validate')->daily();
        $schedule->command('crm:website:checker')->daily();
        $schedule->command('crm:notification-reminder:send')->everyFiveMinutes();
        $schedule->command('crm:campaign:send')->everyFifteenMinutes()->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
