<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Models\Customer;
use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhoneValidatorCommandTest extends TestCase
{
    #[Test]
    public function it_verifies_the_valid_phones_of_customers_and_leads(): void
    {
        $customer = Customer::factory()->create(['country_id' => 'gb', 'phone' => '4407847430288', 'phone_verified' => false, 'mobile' => null, 'phone2' => null]);
        $lead = Lead::factory()->create(['country_id' => 'gb', 'phone' => '447847430288', 'phone_verified' => null, 'mobile' => null, 'phone2' => null]);

        $this->artisan('crm:phone:validate')->assertSuccessful();

        $this->assertTrue((bool) $customer->fresh()->phone_verified);
        $this->assertTrue((bool) $lead->fresh()->phone_verified);
    }

    #[Test]
    public function it_leaves_invalid_phones_unverified(): void
    {
        $customer = Customer::factory()->create(['country_id' => 'gb', 'phone' => '123', 'phone_verified' => false, 'mobile' => null, 'phone2' => null]);

        $this->artisan('crm:phone:validate')->assertSuccessful();

        $this->assertFalse((bool) $customer->fresh()->phone_verified);
    }
}
