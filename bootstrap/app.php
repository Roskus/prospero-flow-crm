<?php

declare(strict_types=1);

use App\Http\Middleware\LimitContentLength;
use App\Http\Middleware\Localization;
use App\Http\Middleware\Locked;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(LimitContentLength::class);

        $middleware->web(append: [
            Localization::class,
        ]);

        $middleware->alias([
            'locked' => Locked::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('crm:contact:email-predict')->hourly();
        $schedule->command('crm:email:validate')->daily();
        $schedule->command('crm:phone:validate')->daily();
        $schedule->command('crm:website:checker')->daily();
        $schedule->command('crm:notification-reminder:send')->everyFiveMinutes();
        $schedule->command('crm:campaign:send')->everyFifteenMinutes();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
