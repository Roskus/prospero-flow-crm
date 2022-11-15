<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test email for test your email configuration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
