<?php

declare(strict_types=1);

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

        $leadsValidated = $this->validateField($leadModel, $phoneUtil, 'phone', 'phone_verified');
        $this->info("Leads validated phones: {$leadsValidated}");
        $leadsValidated = $this->validateField($leadModel, $phoneUtil, 'mobile', 'mobile_verified');
        $this->info("Leads validated mobiles: {$leadsValidated}");
        $leadsValidated = $this->validateField($leadModel, $phoneUtil, 'phone2', 'phone2_verified');
        $this->info("Leads validated phone2: {$leadsValidated}");
    }

    private function validateField($leadModel, $phoneUtil, $field, $verificationField): int
    {
        $verifiedLeads = $leadModel->whereNotNull($field)
            ->where($verificationField, 0)->get();
        $leadsCount = $verifiedLeads->count();

        $this->info("Leads to validate {$field}: {$leadsCount}");
        $leadsValidated = 0;

        foreach ($verifiedLeads as $lead) {
            try {
                $number = $phoneUtil->parse($lead->{$field}, $lead->country_id);
                if ($phoneUtil->isValidNumber($number)) {
                    $lead->{$verificationField} = true;
                    $lead->updated_at = now();
                    $lead->save();
                    $leadsValidated++;
                }
            }  catch (\libphonenumber\NumberParseException $e) {
                // Log or print the error
                $this->error("Error parsing $field for Lead ID {$lead->id}");
            }
        }

        return $leadsValidated;
    }
}
