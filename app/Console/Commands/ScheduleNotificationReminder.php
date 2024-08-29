<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\InternalCRMEmail;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ScheduleNotificationReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:notification-reminder:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send schedule notification contact email to the user by email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $timezone = 'Europe/Madrid';
        $today = Carbon::today($timezone)->toDateString();
        $this->info("Today: $today");

        $leads = Lead::whereDate('schedule_contact', '=', $today)->get();
        $subjectText = 'Remember contact to: :name';
        $bodyText = 'This is an automatic reminder to contact :name at :time';
        $this->info('Leads: '.$leads->count());
        $this->sendNotifications('lead', $subjectText, $bodyText, $leads);

        $customers = Customer::whereDate('schedule_contact', '=', $today)->get();
        $this->info('Customers: '.$customers->count());
        $this->sendNotifications('customer', $subjectText, $bodyText, $customers);

        return 0;
    }

    private function sendNotifications(string $type, string $subject, string $body, Collection $recipients): void
    {
        foreach ($recipients as $recipient) {
            $time = $recipient->schedule_contact->format('H:i');
            $subject = __($subject, ['name' => $recipient->name]);
            $this->info($subject);

            $body = __($body, ['name' => $recipient->name, 'time' => $time]);

            $emailTemplate = new InternalCRMEmail($recipient->company, $subject, ['body' => $body]);
            $notification = new Notification;
            $notification->fill(
                [
                    'company_id' => $recipient->company_id,
                    'user_id' => $recipient->seller_id,
                    'message' => $subject,
                    'link' => url("/$type/update/$recipient->id"),
                ]
            );

            try {
                $notification->save();
                Mail::to($recipient->seller->email)->send($emailTemplate);
            } catch (\Throwable $t) {
                Log::error($t->getMessage());
            }

            $recipient->schedule_contact = null;
            $recipient->save();
        }
    }
}
