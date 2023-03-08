<?php

namespace Tests\Unit\Console\Commands;

use App\Mail\GenericEmail;
use App\Models\Customer;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ScheduleNotificationReminderTest extends TestCase
{
    /** @test */
    public function it_can_send_notifications()
    {
        Mail::fake();

        $lead = Lead::factory()->create(['schedule_contact' => Carbon::today()]);
        $customer = Customer::factory()->create(['schedule_contact' => Carbon::today()]);

        $this->artisan('crm:notification-reminder:send')
            ->expectsOutputToContain('Remember contact')
            ->assertSuccessful();

        Mail::assertSent(GenericEmail::class, 2);

        Mail::assertSent(GenericEmail::class, function (GenericEmail $mail) use ($lead) {
            return $mail->hasTo($lead->seller->email) &&
                   $mail->hasSubject('Remember contact to: '.$lead->name);
        });

        Mail::assertSent(GenericEmail::class, function (GenericEmail $mail) use ($customer) {
            return $mail->hasTo($customer->seller->email) &&
                   $mail->hasSubject('Remember contact to: '.$customer->name);
        });
    }
}
