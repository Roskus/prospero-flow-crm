<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\GenericEmail;
use App\Models\Customer;
use App\Models\Lead;
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
        $leads = Lead::whereDate('schedule_contact', '=', Carbon::today()->toDateString());
        $subject = 'Remember contact to :name';
        $body = 'This is an automatic reminder to contact :name at :time';

        foreach($leads as $lead)
        {
            $time = $lead->scheduled_contact->format('H:i');
            $emailTemplate = new GenericEmail($lead->company, __($subject,['name' => $lead->name, 'time' => $time]), ['body' => $body]);
            try {
                Mail::to($lead->seller()->email)->send($emailTemplate);
            } catch (\Throwable $t) {
                Log::error($t->getMessage());
            }
        }

        $customers = Customer::whereDate('schedule_contact', '=', Carbon::today()->toDateString());
        foreach($customers as $customer)
        {
            $time = $customer->scheduled_contact->format('H:i');
            $emailTemplate = new GenericEmail($customer->company, __($subject,['name' => $customer->name, 'time' => $time]), ['body' => $body]);
            try {
                Mail::to($customer->seller()->email)->send($emailTemplate);
            } catch (\Throwable $t) {
                Log::error($t->getMessage());
            }
        }
        return 0;
    }
}
