<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\GenericEmail;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
        foreach ($leads as $lead) {
            $time = $lead->schedule_contact->format('H:i');
            $subject = __($subjectText, ['name' => $lead->name]);
            $this->info($subject);
            $body = __($bodyText, ['name' => $lead->name, 'time' => $time]);

            $emailTemplate = new GenericEmail($lead->company, $subject, ['body' => $body]);
            $notification = new Notification();
            $notification->fill(
                [
                    'company_id' => $lead->company_id,
                    'user_id' => $lead->seller_id,
                    'message' => $subject,
                ]
            );

            try {
                $notification->save();
                Mail::to($lead->seller()->email)->send($emailTemplate);
            } catch (\Throwable $t) {
                Log::error($t->getMessage());
            }

            $lead->schedule_contact = null;
            $lead->save();
        }

        $customers = Customer::whereDate('schedule_contact', '=', $today)->get();
        $this->info('Customers: '.$customers->count());
        foreach ($customers as $customer) {
            $time = $customer->schedule_contact->format('H:i');
            $subject = __($subjectText, ['name' => $customer->name]);
            $this->info($subject);
            $body = __($bodyText, ['name' => $customer->name, 'time' => $time]);
            $emailTemplate = new GenericEmail($customer->company, $subject, ['body' => $body]);

            $notification = new Notification();
            $notification->fill(
                [
                    'company_id' => $customer->company_id,
                    'user_id' => $customer->seller_id,
                    'message' => $subject,
                ]
            );

            try {
                $notification->save();
                Mail::to($customer->seller()->email)->send($emailTemplate);
            } catch (\Throwable $t) {
                Log::error($t->getMessage());
            }
            $customer->schedule_contact = null;
            $customer->save();
        }

        return 0;
    }
}
