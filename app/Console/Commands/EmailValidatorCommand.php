<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\EmailVerificationStatus;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Supplier\SupplierContact;
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
     */
    public function handle(): int
    {
        foreach ([Lead::class, Customer::class, Contact::class, SupplierContact::class] as $model) {
            $records = $model::whereNotNull('email')
                ->whereIn('email_verified', [EmailVerificationStatus::Pending, EmailVerificationStatus::Invalid])
                ->get();

            foreach ($records as $record) {
                $isValid = Validator::make(['email' => $record->email], ['email' => 'email:rfc,dns'])->passes();
                $record->email_verified = $isValid ? EmailVerificationStatus::Valid : EmailVerificationStatus::Invalid;
                $record->save();
            }

            $this->info(class_basename($model).": {$records->count()} emails validated.");
        }

        return Command::SUCCESS;
    }
}
