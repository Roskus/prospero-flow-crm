<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class CompanyCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:company:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new company';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->ask('What is the name of the company?');
        $country_id = $this->ask('What is the country ISO code of the company? (2 chars)');

        $company = new Company;
        $company->name = $name;
        $company->country_id = $country_id;
        $company->status = Company::ACTIVE;
        $company->created_at = now();
        $company->save();

        return Command::SUCCESS;
    }
}
