<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CompanyCreate::class,
        Commands\UserCreate::class,
        Commands\CampaignSender::class,
        Commands\ScheduleNotificationReminder::class,
        Commands\CrmDevCheck::class,
        Commands\EmailValidatorCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')
        //          ->hourly();
        //$schedule->command('mail:send')->everyMinute();
        $schedule->command('crm:notification-reminder:send')->everyFiveMinutes();
        $schedule->command('crm:email:validate')->dailyAt('00:00');
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands(): void
    {
        require base_path('routes/console.php');
    }
}
