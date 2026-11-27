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
    public function handle(Lead $leadModel, Customer $customerModel, Contact $contactModel, SupplierContact $supplierContactModel): int
    {
        $rules = ['email' => 'email:rfc,dns'];

        $verifiedLeads = $leadModel->whereNotNull('email')
            ->whereIn('email_verified', [EmailVerificationStatus::Pending->value, EmailVerificationStatus::Invalid->value])->get();
        foreach ($verifiedLeads as $lead) {
            $validator = Validator::make(['email' => $lead->email], $rules);
            $lead->email_verified = $validator->passes() ? EmailVerificationStatus::Valid->value : EmailVerificationStatus::Invalid->value;
            $lead->updated_at = now();
            $lead->save();
        }
        $leadsCount = $verifiedLeads->count();
        $this->info("Leads Email validation: $leadsCount complete.");

        $verifiedCustomers = $customerModel->whereNotNull('email')
            ->whereIn('email_verified', [EmailVerificationStatus::Pending->value, EmailVerificationStatus::Invalid->value])->get();
        foreach ($verifiedCustomers as $customer) {
            $validator = Validator::make(['email' => $customer->email], $rules);
            $customer->email_verified = $validator->passes() ? EmailVerificationStatus::Valid->value : EmailVerificationStatus::Invalid->value;
            $customer->updated_at = now();
            $customer->save();
        }
        $customerCount = $verifiedCustomers->count();
        $this->info("Customers Email validation: $customerCount complete.");

        $verifiedContacts = $contactModel->whereNotNull('email')
            ->whereIn('email_verified', [EmailVerificationStatus::Pending->value, EmailVerificationStatus::Invalid->value])->get();
        foreach ($verifiedContacts as $contact) {
            $validator = Validator::make(['email' => $contact->email], $rules);
            $contact->email_verified = $validator->passes() ? EmailVerificationStatus::Valid->value : EmailVerificationStatus::Invalid->value;
            $contact->updated_at = now();
            $contact->save();
        }
        $contactCount = $verifiedContacts->count();
        $this->info("Contacts Email validation: $contactCount complete.");

        $verifiedSupplierContacts = $supplierContactModel->whereNotNull('email')
            ->whereIn('email_verified', [EmailVerificationStatus::Pending->value, EmailVerificationStatus::Invalid->value])->get();
        foreach ($verifiedSupplierContacts as $supplierContact) {
            $validator = Validator::make(['email' => $supplierContact->email], $rules);
            $supplierContact->email_verified = $validator->passes() ? EmailVerificationStatus::Valid->value : EmailVerificationStatus::Invalid->value;
            $supplierContact->updated_at = now();
            $supplierContact->save();
        }
        $supplierContactCount = $verifiedSupplierContacts->count();
        $this->info("Supplier contacts Email validation: $supplierContactCount complete.");
        $this->info(sprintf('%d leads, %d customers, %d contacts and %d supplier contacts verified.', $leadsCount, $customerCount, $contactCount, $supplierContactCount));

        return Command::SUCCESS;
    }
}
