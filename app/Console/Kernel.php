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
        Commands\ContactEmailPredictCommand::class,
        Commands\PhoneValidatorCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('crm:notification-reminder:send')->everyFiveMinutes();
        $schedule->command('crm:contact:email-predict')->hourly();
        $schedule->command('crm:email:validate')->dailyAt('00:00');
        $schedule->command('crm:phone:validate')->dailyAt('01:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require_once base_path('routes/console.php');
    }
}
