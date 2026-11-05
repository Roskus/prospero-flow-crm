<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Customer;
use App\Models\Industry;
use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProspectSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Industry::factory()->create();
    }

    #[Test]
    public function it_finds_leads_by_name_or_business_name_ignoring_case(): void
    {
        $byName = Lead::factory()->create(['name' => 'Acme Robotics', 'business_name' => null]);
        $byBusinessName = Lead::factory()->create(['name' => 'Coyote', 'business_name' => 'ACME Industrial S.L.']);
        Lead::factory()->create(['name' => 'Globex', 'business_name' => 'Globex Corp']);

        $found = Lead::query()->search('acme')->pluck('id')->all();

        $this->assertEqualsCanonicalizing([$byName->id, $byBusinessName->id], $found);
    }

    #[Test]
    public function an_empty_search_does_not_filter(): void
    {
        Lead::factory()->count(2)->create();

        $this->assertSame(2, Lead::query()->search('  ')->count());
        $this->assertSame(2, Lead::query()->search(null)->count());
    }

    #[Test]
    public function percent_and_underscore_are_matched_literally(): void
    {
        $literal = Lead::factory()->create(['name' => '100% Natural', 'business_name' => null]);
        Lead::factory()->create(['name' => '1000 Natural', 'business_name' => null]);
        Lead::factory()->create(['name' => 'ab', 'business_name' => null]);

        $this->assertSame([$literal->id], Lead::query()->search('100%')->pluck('id')->all());
        $this->assertSame(0, Lead::query()->search('a_')->count());
    }

    #[Test]
    public function it_finds_customers_too(): void
    {
        $customer = Customer::factory()->create(['name' => 'Initech', 'business_name' => null]);
        Customer::factory()->create(['name' => 'Umbrella', 'business_name' => null]);

        $this->assertSame([$customer->id], Customer::query()->search('INIT')->pluck('id')->all());
    }
}
