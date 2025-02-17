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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::whereIsNotNull('website');
        foreach ($leads as $lead) {
            $this->info("Cheking: $lead->website \n");
            if (Domain::isValid($lead->website)) {
                $this->info('Ok');
            } else {
                $this->info('Error');
            }
        }

        return Command::SUCCESS;
    }
}
