<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use Tests\TestCase;

class CompanyDeleteControllerTest extends TestCase
{
    /** @test */
    public function it_can_delete_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->get('/company/delete/'.$company->id);
        $response->assertRedirect('/company');

        $this->assertEquals(Company::find($company->id), null);
    }
}
