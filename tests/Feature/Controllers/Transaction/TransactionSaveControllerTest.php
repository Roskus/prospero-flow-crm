<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Transaction;

use App\Models\Transaction;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TransactionSaveControllerTest extends TestCase
{
    #[Test]
    public function it_allows_super_admin_to_create_transaction(): void
    {
        $response = $this->post('/transaction/save', [
            'name' => 'Test transaction',
            'type' => 'income',
            'amount' => 1000.50,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
        ]);

        $response->assertRedirect('/accounting');
        $this->assertDatabaseHas('transaction', [
            'name' => 'Test transaction',
            'amount' => 1000.50,
        ]);
    }

    #[Test]
    public function it_blocks_user_without_permission(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/transaction/save', [
            'name' => 'Hacked transaction',
            'type' => 'income',
            'amount' => 100,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function it_allows_super_admin_to_use_negative_amount(): void
    {
        $response = $this->post('/transaction/save', [
            'name' => 'Refund transaction',
            'type' => 'income',
            'amount' => -750,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
        ]);

        $response->assertRedirect('/accounting');
        $this->assertDatabaseHas('transaction', [
            'name' => 'Refund transaction',
            'amount' => -750,
        ]);
    }

    #[Test]
    public function it_blocks_negative_amount_without_update_permission(): void
    {
        $limitedRole = Role::create(['name' => 'CreateOnly', 'guard_name' => 'web']);
        $limitedRole->givePermissionTo(Permission::findOrCreate('create accounting', 'web'));
        $user = User::factory()->create();
        $user->assignRole($limitedRole);

        $this->actingAs($user);

        $response = $this->post('/transaction/save', [
            'name' => 'Hacked refund',
            'type' => 'expense',
            'amount' => -500,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseMissing('transaction', ['name' => 'Hacked refund']);
    }

    #[Test]
    public function it_allows_super_admin_to_update_transaction(): void
    {
        $transaction = Transaction::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $response = $this->post('/transaction/save', [
            'id' => $transaction->id,
            'name' => 'Updated transaction',
            'type' => 'expense',
            'amount' => 200,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
        ]);

        $response->assertRedirect('/accounting');
        $this->assertDatabaseHas('transaction', [
            'id' => $transaction->id,
            'name' => 'Updated transaction',
        ]);
    }

    #[Test]
    public function it_blocks_update_without_permission(): void
    {
        $transaction = Transaction::factory()->create([
            'company_id' => $this->user->company_id,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/transaction/save', [
            'id' => $transaction->id,
            'name' => 'Hacked update',
            'type' => 'expense',
            'amount' => 200,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function it_allows_positive_amount_for_create_only_role(): void
    {
        $limitedRole = Role::create(['name' => 'CreateOnlyAllowed', 'guard_name' => 'web']);
        $limitedRole->givePermissionTo(Permission::findOrCreate('create accounting', 'web'));
        $user = User::factory()->create();
        $user->assignRole($limitedRole);

        $this->actingAs($user);

        $response = $this->post('/transaction/save', [
            'name' => 'Allowed create',
            'type' => 'income',
            'amount' => 100,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
        ]);

        $response->assertRedirect('/accounting');
        $this->assertDatabaseHas('transaction', ['name' => 'Allowed create']);
    }
}
