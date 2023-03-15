<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use Tests\TestCase;

class CompanyUpdateControllerTest extends TestCase
{
    /** @test */
    public function it_can_update_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->get('/company/update/'.$company->id);

        $response->assertOk();
        $response->assertViewIs('company.company');
        $response->assertSee('Name');
        $response->assertSee('E-mail');
        $response->assertSee('Country');
        $response->assertSee($company->name);
        $response->assertSee($company->email);
        $response->assertSee($company->country->name);
    }
}
