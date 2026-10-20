<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use App\Services\CompanyAccessService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MultiCompanyDataIsolationTest extends TestCase
{
    private Company $parentCompany;

    private Company $clientCompany;

    private User $multiCompanyUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class, PermissionSeeder::class]);

        $this->parentCompany = Company::factory()->create();
        $this->clientCompany = Company::factory()->clientOf($this->parentCompany)->create();
        $this->multiCompanyUser = User::factory()->create(['company_id' => $this->parentCompany->id]);
        $this->multiCompanyUser->assignRole('CompanyAdmin');
        app(CompanyAccessService::class)->grant($this->multiCompanyUser, $this->clientCompany);
        $this->multiCompanyUser->refresh();
    }

    #[Test]
    public function lists_only_show_the_active_company_data(): void
    {
        $parentCompanyLead = Lead::factory()->create(['company_id' => $this->parentCompany->id, 'name' => 'Parent company lead']);
        $clientCompanyLead = Lead::factory()->create(['company_id' => $this->clientCompany->id, 'name' => 'Client company lead']);
        $clientCompanyCustomer = Customer::factory()->create(['company_id' => $this->clientCompany->id, 'name' => 'Client company customer']);

        $this->actingAs($this->multiCompanyUser)->get('/lead')
            ->assertSee($parentCompanyLead->name)
            ->assertDontSee($clientCompanyLead->name);

        $this->actingAs($this->multiCompanyUser)->post(route('company.switch', $this->clientCompany->id));
        $this->multiCompanyUser->refresh();

        $this->actingAs($this->multiCompanyUser)->get('/lead')
            ->assertSee($clientCompanyLead->name)
            ->assertDontSee($parentCompanyLead->name);
        $this->actingAs($this->multiCompanyUser)->get('/customer')
            ->assertSee($clientCompanyCustomer->name);
    }

    #[Test]
    public function records_created_after_switching_belong_to_the_new_company_with_the_real_user(): void
    {
        $this->actingAs($this->multiCompanyUser)->post(route('company.switch', $this->clientCompany->id));
        $this->multiCompanyUser->refresh();

        $this->actingAs($this->multiCompanyUser)->post('/lead/save', [
            'name' => 'Lead created by the multi company user',
            'country_id' => 'es',
            'seller_id' => $this->multiCompanyUser->id,
        ])->assertRedirect('/lead');

        $this->assertDatabaseHas('lead', [
            'name' => 'Lead created by the multi company user',
            'company_id' => $this->clientCompany->id,
            'seller_id' => $this->multiCompanyUser->id,
        ]);
    }

    #[Test]
    public function records_of_another_accessible_company_cannot_be_opened_without_switching(): void
    {
        $clientCompanyLead = Lead::factory()->create(['company_id' => $this->clientCompany->id]);

        $this->actingAs($this->multiCompanyUser)->get('/lead/update/'.$clientCompanyLead->id)->assertNotFound();
    }
}
