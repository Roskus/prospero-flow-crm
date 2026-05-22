<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_company(): void
    {
        Company::factory()->create(['id' => Company::DEFAULT_COMPANY]);
        $this->user->company_id = Company::DEFAULT_COMPANY;
        $this->user->save();

        $company = Company::factory()->create();

        $response = $this->get('/company/delete/'.$company->id);
        $response->assertRedirect('/company');

        $this->assertNull(Company::find($company->id));
    }
}
