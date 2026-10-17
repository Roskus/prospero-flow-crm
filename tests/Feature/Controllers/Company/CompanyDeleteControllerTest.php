<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use App\Models\User;
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

        $response = $this->delete('/company/delete/'.$company->id);
        $response->assertRedirect('/company');

        $this->assertNull(Company::find($company->id));
    }

    #[Test]
    public function it_cannot_delete_own_company(): void
    {
        $response = $this->delete('/company/delete/'.$this->user->company_id);

        $response->assertRedirect('/company');
        $response->assertSessionHas('error');
        $this->assertNotSoftDeleted('company', ['id' => $this->user->company_id]);
        $this->assertNotSoftDeleted('user', ['id' => $this->user->id]);
    }

    #[Test]
    public function deleting_a_company_soft_deletes_its_users(): void
    {
        $company = Company::factory()->create();
        $companyUser = User::factory()->create(['company_id' => $company->id]);

        $response = $this->delete('/company/delete/'.$company->id);

        $response->assertRedirect('/company');
        $this->assertSoftDeleted('user', ['id' => $companyUser->id]);
    }
}
