<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
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

    #[Test]
    public function leads_stay_limited_to_the_assigned_seller(): void
    {
        $own = Lead::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->seller->id]);
        $other = Lead::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->otherSeller->id]);

        $this->actingAs($this->seller);

        $this->assertTrue(Lead::query()->whereKey($own->id)->exists());
        $this->assertFalse(Lead::query()->whereKey($other->id)->exists());
    }
}
