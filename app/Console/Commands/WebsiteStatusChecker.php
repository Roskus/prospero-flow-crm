<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\Domain;
use App\Models\Lead;
use Illuminate\Console\Command;

class WebsiteStatusChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:website:checker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the HTTP status of all lead websites';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $leads = Lead::whereNotNull('website')->get();
        $ok = 0;
        $errors = 0;

        $this->info("Checking {$leads->count()} leads...");

        foreach ($leads as $lead) {
            if (Domain::isValid($lead->website)) {
                $this->info("OK: {$lead->website}");
                $ok++;
            } else {
                $this->error("Error: {$lead->website}");
                $errors++;
            }
        }

        $this->newLine();
        $this->info("Completed — OK: {$ok} / Errors: {$errors}");

        return Command::SUCCESS;
    }
}
