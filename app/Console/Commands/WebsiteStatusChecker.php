<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\Domain;
use App\Models\Customer;
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
    protected $description = 'Check the HTTP status of all lead and customer websites';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $ok = 0;
        $errors = 0;

        foreach ([Lead::class => 'leads', Customer::class => 'customers'] as $model => $label) {
            $query = $model::query()
                ->whereNotNull('website')
                ->where('website', '!=', '')
                ->whereNull('website_verified');

            $this->info("Checking {$query->count()} unverified {$label}...");

            foreach ($query->lazyById() as $prospect) {
                $prospect->website_verified = Domain::isValid($prospect->website);
                $prospect->save();

                if ($prospect->website_verified) {
                    $this->info("OK: {$prospect->website}");
                    $ok++;
                } else {
                    $this->error("Error: {$prospect->website}");
                    $errors++;
                }
            }
        }

        $this->newLine();
        $this->info("Completed — OK: {$ok} / Errors: {$errors}");

        return Command::SUCCESS;
    }
}
