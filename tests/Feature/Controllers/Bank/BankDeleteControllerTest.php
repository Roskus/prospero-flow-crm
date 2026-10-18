<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank;
use App\Models\Company;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BankDeleteControllerTest extends TestCase
{
    #[Test]
    public function super_admin_can_delete_a_bank(): void
    {
        $bank = Bank::factory()->create();

        $response = $this->delete('/bank/delete/'.$bank->uuid);

        $response->assertRedirect('bank');
        $this->assertDatabaseMissing('bank', ['uuid' => $bank->uuid]);
    }

    #[Test]
    public function it_does_not_delete_a_bank_through_a_get_request(): void
    {
        $bank = Bank::factory()->create();

        $response = $this->get('/bank/delete/'.$bank->uuid);

        $response->assertMethodNotAllowed();
        $this->assertDatabaseHas('bank', ['uuid' => $bank->uuid]);
    }

    #[Test]
    public function default_company_user_without_super_admin_role_cannot_delete_a_bank(): void
    {
        $defaultCompany = Company::find(Company::DEFAULT_COMPANY) ?? Company::factory()->create(['id' => Company::DEFAULT_COMPANY]);
        $user = User::factory()->create(['company_id' => $defaultCompany->id]);
        $user->givePermissionTo(Permission::findOrCreate('delete bank', 'web'));
        $bank = Bank::factory()->create();

        $response = $this->actingAs($user)->delete('/bank/delete/'.$bank->uuid);

        $response->assertForbidden();
        $this->assertDatabaseHas('bank', ['uuid' => $bank->uuid]);
    }
}
