<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\User;
use App\Repositories\LeadRepository;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SellerCustomerVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;

    private User $otherSeller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $this->seller = User::factory()->create(['company_id' => $this->user->company_id]);
        $this->seller->assignRole('Seller');
        $this->otherSeller = User::factory()->create(['company_id' => $this->user->company_id]);
    }

    #[Test]
    public function a_seller_lists_and_opens_customers_of_other_sellers(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->otherSeller->id, 'name' => 'Customer of another seller']);

        $this->actingAs($this->seller)->get('/customer')->assertOk()->assertSee('Customer of another seller');
        $this->actingAs($this->seller)->get('/customer/show/'.$customer->id)->assertOk();
    }

    #[Test]
    public function a_seller_can_still_filter_customers_by_seller(): void
    {
        Customer::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->seller->id, 'name' => 'Mine']);
        Customer::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->otherSeller->id, 'name' => 'Theirs']);

        $this->actingAs($this->seller)->get('/customer?seller_id='.$this->seller->id)
            ->assertOk()
            ->assertSee('Mine')
            ->assertDontSee('Theirs');
    }

    #[Test]
    public function a_seller_finds_customers_of_other_sellers_in_the_order_autocomplete(): void
    {
        Customer::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->otherSeller->id, 'name' => 'Zacarias']);

        $this->actingAs($this->seller)->getJson('/customer?term=Zacarias', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('Zacarias');
    }

    #[Test]
    public function a_seller_does_not_see_customers_of_another_company(): void
    {
        Customer::factory()->create(['company_id' => Company::factory()->create()->id, 'name' => 'Foreign customer']);

        $this->actingAs($this->seller)->get('/customer')->assertOk()->assertDontSee('Foreign customer');
    }

    /**
     * Business rule: a seller can see and work any lead of the company; the lead list is filtered by seller.
     */
    #[Test]
    public function a_seller_opens_leads_of_other_sellers(): void
    {
        Industry::factory()->create();
        $other = Lead::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->otherSeller->id]);

        $this->actingAs($this->seller);

        $this->assertTrue(Lead::query()->whereKey($other->id)->exists());
        $this->get('/lead/show/'.$other->id)->assertOk();
    }

    #[Test]
    public function the_lead_list_shows_a_seller_only_their_own_leads(): void
    {
        Industry::factory()->create();
        Lead::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->seller->id, 'name' => 'My own lead']);
        Lead::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->otherSeller->id, 'name' => 'Lead of another seller']);

        $this->actingAs($this->seller)->get('/lead')
            ->assertOk()
            ->assertSee('My own lead')
            ->assertDontSee('Lead of another seller');
    }

    #[Test]
    public function saving_a_lead_without_seller_keeps_its_assigned_seller(): void
    {
        Industry::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->otherSeller->id]);

        $this->actingAs($this->seller);
        app(LeadRepository::class)->save(['id' => $lead->id, 'name' => 'Renamed']);

        $this->assertDatabaseHas('lead', ['id' => $lead->id, 'name' => 'Renamed', 'seller_id' => $this->otherSeller->id]);
    }
}
