<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use Tests\TestCase;

class CompanyIndexControllerTest extends TestCase
{
    /** @test */
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
}
