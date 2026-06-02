<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Company;

use App\Models\Company;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyReadControllerTest extends TestCase
{
    #[Test]
    public function it_can_read_own_company(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/company/'.$user->company_id);

        $response->assertOk();
        $this->assertEquals($user->company_id, $response->json('id'));
    }

    #[Test]
    public function it_cannot_read_another_company(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $otherCompany = Company::factory()->create();

        $response = $this->getJson('/api/company/'.$otherCompany->id);

        $response->assertForbidden();
    }

    #[Test]
    public function super_admin_can_read_any_company(): void
    {
        $this->actingAs($this->user, 'api');
        $otherCompany = Company::factory()->create();

        $response = $this->getJson('/api/company/'.$otherCompany->id);

        $response->assertOk();
        $this->assertEquals($otherCompany->id, $response->json('id'));
    }

    #[Test]
    public function it_returns_404_for_nonexistent_company(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->getJson('/api/company/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->getJson('/api/company/1');

        $response->assertUnauthorized();
    }
}
