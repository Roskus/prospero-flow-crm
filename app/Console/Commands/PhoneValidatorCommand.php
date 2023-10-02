<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;

class PhoneValidatorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:phone:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate phone numbers';

    /**
     * Execute the console command.
     */
    public function handle(Lead $leadModel)
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $verifiedLeads = $leadModel->whereNotNull('phone')
            ->where('phone_verified', 0)->get();
        $leadsCount = $verifiedLeads->count();
        $this->info("Leads to validate phone: $leadsCount");
        $leadsValidated = 0;
        foreach ($verifiedLeads as $lead) {
            if($lead->phone)
            {
                $phoneNumber = $phoneUtil->parse($lead->phone, $lead->country_id);
                if($phoneUtil->isValidNumber($phoneNumber))
                {
                    $lead->phone_verified = true;
                    $lead->updated_at = now();
                    $lead->save();
                    $leadsValidated++;
                }
            }
        }
        $this->info("Leads validated phones: $leadsValidated");
    }
}
