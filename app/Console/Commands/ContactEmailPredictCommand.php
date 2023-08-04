<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Contact;
use Illuminate\Console\Command;
use App\Helpers\PredictEmail;

class ContactEmailPredictCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:contact:email-predict';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contact email completition';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Counter for contacts modified
        $contactsModified = 0;

        // Get all contacts where the company has a URL and the contact's email is empty
        $contacts = Contact::with('lead')->whereHas('lead', function ($query) {
            $query->whereNotNull('website'); // Assuming the company's URL is stored in the 'url' column
        })
            ->whereNull('email')
            ->get();

        $predictEmail = new PredictEmail();

        foreach ($contacts as $contact) {

            $predictedEmail = $predictEmail->predict(
                $contact->first_name,
                (string) $contact->last_name,
                $contact->lead->website // Assuming the company's URL is stored in the 'url' column
            );

            if ($predictedEmail) {
                $contact->email = $predictedEmail;
                $contact->updated_at = now();
                $contact->save();
                $contactsModified++;
            }
        }

        // Display the counter
        $this->info("Number of contacts modified: $contactsModified");

        return Command::SUCCESS;
    }

}
