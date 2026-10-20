<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Models\Customer;
use App\Models\Lead;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebsiteStatusCheckerTest extends TestCase
{
    #[Test]
    public function it_marks_unreachable_lead_and_customer_websites_as_not_verified(): void
    {
        $lead = Lead::factory()->create(['website' => 'http://127.0.0.1', 'website_verified' => null]);
        $customer = Customer::factory()->create(['website' => 'http://localhost', 'website_verified' => null]);

        $this->artisan('crm:website:checker')
            ->expectsOutput('Checking 1 unverified leads...')
            ->expectsOutput('Error: http://127.0.0.1')
            ->expectsOutput('Checking 1 unverified customers...')
            ->expectsOutput('Error: http://localhost')
            ->expectsOutput('Completed — OK: 0 / Errors: 2')
            ->assertSuccessful();

        $this->assertDatabaseHas('lead', ['id' => $lead->id, 'website_verified' => false]);
        $this->assertDatabaseHas('customer', ['id' => $customer->id, 'website_verified' => false]);
    }

    #[Test]
    public function it_skips_already_checked_and_empty_websites(): void
    {
        $verifiedLead = Lead::factory()->create(['website' => 'http://127.0.0.1', 'website_verified' => true]);
        $failedCustomer = Customer::factory()->create(['website' => 'http://localhost', 'website_verified' => false]);
        $emptyCustomer = Customer::factory()->create(['website' => '', 'website_verified' => null]);
        $nullLead = Lead::factory()->create(['website' => null, 'website_verified' => null]);

        $this->artisan('crm:website:checker')
            ->expectsOutput('Checking 0 unverified leads...')
            ->expectsOutput('Checking 0 unverified customers...')
            ->expectsOutput('Completed — OK: 0 / Errors: 0')
            ->assertSuccessful();

        $this->assertDatabaseHas('lead', ['id' => $verifiedLead->id, 'website_verified' => true]);
        $this->assertDatabaseHas('customer', ['id' => $failedCustomer->id, 'website_verified' => false]);
        $this->assertDatabaseHas('customer', ['id' => $emptyCustomer->id, 'website_verified' => null]);
        $this->assertDatabaseHas('lead', ['id' => $nullLead->id, 'website_verified' => null]);
    }

    #[Test]
    #[Group('network')]
    public function it_marks_reachable_lead_and_customer_websites_as_verified(): void
    {
        $lead = Lead::factory()->create(['website' => 'https://www.example.com', 'website_verified' => null]);
        $customer = Customer::factory()->create(['website' => 'example.com', 'website_verified' => null]);

        $this->artisan('crm:website:checker')
            ->expectsOutput('Completed — OK: 2 / Errors: 0')
            ->assertSuccessful();

        $this->assertDatabaseHas('lead', ['id' => $lead->id, 'website_verified' => true]);
        $this->assertDatabaseHas('customer', ['id' => $customer->id, 'website_verified' => true]);
    }
}
