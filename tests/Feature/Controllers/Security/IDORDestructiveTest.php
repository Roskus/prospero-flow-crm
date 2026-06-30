<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Security;

use App\Models\Brand;
use App\Models\Calendar;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Product;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * IDOR Destructive Tests - Prevent unauthorized deletion of cross-tenant resources
 * CVSS 3.1: 8.1 (High) — AV:N/AC:L/PR:L/UI:N/S:U/C:N/I:H/A:H
 */
class IDORDestructiveTest extends TestCase
{
    protected User $userA;

    protected User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::findByName('SuperAdmin');

        $this->userA = $this->user;
        $this->userA->assignRole($role);

        $this->userB = User::factory()->create();
        $this->userB->assignRole($role);
    }

    #[Test]
    public function it_prevents_deleting_brands_from_other_companies(): void
    {
        $brandFromB = Brand::factory()->create(['company_id' => $this->userB->company_id]);

        $this->actingAs($this->userA);
        $response = $this->get("/brand/delete/{$brandFromB->id}");

        $response->assertNotFound();
        $this->assertNotNull(Brand::find($brandFromB->id));
    }

    #[Test]
    public function it_prevents_deleting_leads_from_other_companies(): void
    {
        $leadFromB = Lead::factory()->create(['company_id' => $this->userB->company_id]);

        $this->actingAs($this->userA);
        $response = $this->get("/lead/delete/{$leadFromB->id}");

        $response->assertNotFound();
        $this->assertNotNull(Lead::find($leadFromB->id));
    }

    #[Test]
    public function it_prevents_deleting_customers_from_other_companies(): void
    {
        $customerFromB = Customer::factory()->create(['company_id' => $this->userB->company_id]);

        $this->actingAs($this->userA);
        $response = $this->get("/customer/delete/{$customerFromB->id}");

        $response->assertNotFound();
        $this->assertNotNull(Customer::find($customerFromB->id));
    }

    #[Test]
    public function it_prevents_deleting_categories_from_other_companies(): void
    {
        $categoryFromB = Category::factory()->create(['company_id' => $this->userB->company_id]);

        $this->actingAs($this->userA);
        $response = $this->get("/category/delete/{$categoryFromB->id}");

        $response->assertNotFound();
        $this->assertNotNull(Category::find($categoryFromB->id));
    }

    #[Test]
    public function it_prevents_deleting_products_from_other_companies(): void
    {
        $productFromB = Product::factory()->create(['company_id' => $this->userB->company_id]);

        $this->actingAs($this->userA);
        $response = $this->post('/product/delete', ['id' => $productFromB->id]);

        $response->assertNotFound();
        $this->assertNotNull(Product::find($productFromB->id));
    }

    #[Test]
    public function it_prevents_deleting_calendar_events_from_other_users(): void
    {
        $calendarEventFromB = Calendar::factory()->create(['user_id' => $this->userB->id]);

        $this->actingAs($this->userA);
        $response = $this->get("/calendar/event/delete/{$calendarEventFromB->id}");

        $response->assertNotFound();
        $this->assertNotNull(Calendar::find($calendarEventFromB->id));
    }

    #[Test]
    public function it_prevents_updating_another_companys_profile(): void
    {
        $companyB = Company::find($this->userB->company_id);
        $originalName = $companyB->name;

        $this->actingAs($this->userA);
        $response = $this->post('/company/save', [
            'id' => $companyB->id,
            'name' => 'Hijacked Company',
            'business_name' => 'Hijacked Business',
            'currency' => 'USD',
        ]);

        $response->assertRedirect('/company');

        $companyB->refresh();
        $this->assertEquals($originalName, $companyB->name);
    }

    #[Test]
    public function it_allows_deleting_own_brand(): void
    {
        $ownBrand = Brand::factory()->create(['company_id' => $this->userA->company_id]);

        $this->actingAs($this->userA);
        $response = $this->get("/brand/delete/{$ownBrand->id}");

        $response->assertRedirect();
        $this->assertTrue(Brand::where('id', $ownBrand->id)->onlyTrashed()->exists());
    }

    #[Test]
    public function it_allows_deleting_own_lead(): void
    {
        $ownLead = Lead::factory()->create(['company_id' => $this->userA->company_id]);

        $this->actingAs($this->userA);
        $response = $this->get("/lead/delete/{$ownLead->id}");

        $response->assertRedirect();
        $this->assertTrue(Lead::where('id', $ownLead->id)->onlyTrashed()->exists());
    }

    #[Test]
    public function it_allows_deleting_own_customer(): void
    {
        $ownCustomer = Customer::factory()->create(['company_id' => $this->userA->company_id]);

        $this->actingAs($this->userA);
        $response = $this->get("/customer/delete/{$ownCustomer->id}");

        $response->assertRedirect();
        $this->assertTrue(Customer::where('id', $ownCustomer->id)->onlyTrashed()->exists());
    }

    #[Test]
    public function it_allows_updating_own_company_profile(): void
    {
        $ownCompany = Company::find($this->userA->company_id);

        $this->actingAs($this->userA);
        $response = $this->post('/company/save', [
            'id' => $ownCompany->id,
            'name' => 'Updated Company Name',
            'business_name' => 'Updated Business',
            'currency' => 'USD',
        ]);

        $response->assertRedirect();

        $ownCompany->refresh();
        $this->assertEquals('Updated Company Name', $ownCompany->name);
    }
}
