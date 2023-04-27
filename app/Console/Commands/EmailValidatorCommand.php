<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class EmailValidatorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:email:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate all CRM emails for each contact';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Lead $leadModel, Customer $customerModel)
    {
        $rules = ['email' => 'email:rfc,dns'];

        $verifiedLeads = $leadModel->whereNotNull('email')->where('email_verified', 0)->get();
        foreach ($verifiedLeads as $lead) {
            $validator = Validator::make(['email' => $lead->email], $rules);
            $lead->email_verified = $validator->passes() ? true : 3;
            $lead->updated_at = now();
            $lead->save();
        }
        $this->info('Leads Email validation complete.');

        $verifiedCustomers = $customerModel->whereNotNull('email')->where('email_verified', 0)->get();
        foreach ($verifiedCustomers as $customer) {
            $validator = Validator::make(['email' => $customer->email], $rules);
            $customer->email_verified = $validator->passes() ? true : 3;
            $customer->updated_at = now();
            $customer->save();
        }

        $this->info('Customers Email validation complete.');
        $this->info(sprintf('%d leads and %d customers verified.', $verifiedLeads->count(), $verifiedCustomers->count()));

        return Command::SUCCESS;
    }
}
