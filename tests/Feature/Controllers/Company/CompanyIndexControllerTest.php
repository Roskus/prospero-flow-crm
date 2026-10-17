<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyIndexControllerTest extends TestCase
{
    #[Test]
    public function it_can_get_index_company(): void
    {
        $this->withoutExceptionHandling();

        $company = Company::factory()->create();

        $response = $this->get('/company');

        $response->assertOk();
        $response->assertViewIs('company.index');
        $response->assertSee(__('Name'));
        $response->assertSee(__('E-mail'));
        $response->assertSee(__('Country'));
        $response->assertSee($company->name);
        $response->assertSee($company->email);
        $response->assertSee($company->country->name);
    }

    #[Test]
    public function it_lists_active_and_deleted_companies_without_filter(): void
    {
        $activeCompany = Company::factory()->create();
        $deletedCompany = Company::factory()->create();
        $deletedCompany->delete();

        $response = $this->get('/company');

        $response->assertOk();
        $response->assertSee($activeCompany->name);
        $response->assertSee($deletedCompany->name);
    }

    #[Test]
    public function it_filters_only_active_companies(): void
    {
        $activeCompany = Company::factory()->create();
        $deletedCompany = Company::factory()->create();
        $deletedCompany->delete();

        $response = $this->get('/company?filter=active');

        $response->assertOk();
        $response->assertSee($activeCompany->name);
        $response->assertDontSee($deletedCompany->name);
    }

    #[Test]
    public function it_filters_only_deleted_companies(): void
    {
        $activeCompany = Company::factory()->create();
        $deletedCompany = Company::factory()->create();
        $deletedCompany->delete();

        $response = $this->get('/company?filter=deleted');

        $response->assertOk();
        $response->assertSee($deletedCompany->name);
        $response->assertDontSee($activeCompany->name);
    }

    #[Test]
    public function it_rejects_an_invalid_filter(): void
    {
        $response = $this->get('/company?filter=invalid');

        $response->assertSessionHasErrors('filter');
    }
}
