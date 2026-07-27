<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Transaction;

use App\Models\Bank\Account as BankAccount;
use App\Models\BankCard;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Supplier;
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
        $limitedRole = Role::findOrCreate('CreateOnly', 'web');
        $limitedRole->givePermissionTo(Permission::findOrCreate('create transaction', 'web'));
        $limitedRole->givePermissionTo(Permission::findOrCreate('update transaction', 'web'));
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
        $limitedRole = Role::findOrCreate('CreateOnlyAllowed', 'web');
        $limitedRole->givePermissionTo(Permission::findOrCreate('create transaction', 'web'));
        $limitedRole->givePermissionTo(Permission::findOrCreate('update transaction', 'web'));
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

    #[Test]
    public function it_accepts_bank_account_belonging_to_own_company(): void
    {
        $bankAccount = BankAccount::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $response = $this->post('/transaction/save', [
            'name' => 'Own bank account test',
            'type' => 'expense',
            'amount' => 100,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
            'bank_account_id' => $bankAccount->id,
        ]);

        $response->assertRedirect('/accounting');
        $this->assertDatabaseHas('transaction', ['name' => 'Own bank account test']);
    }

    #[Test]
    public function it_rejects_bank_account_belonging_to_other_company(): void
    {
        $otherCompany = Company::factory()->create();
        $bankAccount = BankAccount::factory()->create([
            'company_id' => $otherCompany->id,
        ]);

        $response = $this->post('/transaction/save', [
            'name' => 'Other company bank account test',
            'type' => 'expense',
            'amount' => 100,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
            'bank_account_id' => $bankAccount->id,
        ]);

        $response->assertSessionHasErrors('bank_account_id');
        $this->assertDatabaseMissing('transaction', ['name' => 'Other company bank account test']);
    }

    #[Test]
    public function it_accepts_bank_card_belonging_to_own_company(): void
    {
        $bankAccount = BankAccount::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $bankCard = new BankCard;
        $bankCard->company_id = $this->user->company_id;
        $bankCard->bank_account_id = $bankAccount->id;
        $bankCard->type = 'credit';
        $bankCard->network = 'visa';
        $bankCard->cardholder_name = 'Test Card';
        $bankCard->number = '4111111111111111';
        $bankCard->expires_month = 12;
        $bankCard->expires_year = 2030;
        $bankCard->save();

        $response = $this->post('/transaction/save', [
            'name' => 'Own bank card test',
            'type' => 'expense',
            'amount' => 100,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
            'bank_card_id' => $bankCard->id,
        ]);

        $response->assertRedirect('/accounting');
        $this->assertDatabaseHas('transaction', ['name' => 'Own bank card test']);
    }

    #[Test]
    public function it_rejects_bank_card_belonging_to_other_company(): void
    {
        $otherCompany = Company::factory()->create();
        $otherBankAccount = BankAccount::factory()->create([
            'company_id' => $otherCompany->id,
        ]);

        $bankCard = new BankCard;
        $bankCard->company_id = $otherCompany->id;
        $bankCard->bank_account_id = $otherBankAccount->id;
        $bankCard->type = 'credit';
        $bankCard->network = 'visa';
        $bankCard->cardholder_name = 'Other Company Card';
        $bankCard->number = '4111111111111111';
        $bankCard->expires_month = 12;
        $bankCard->expires_year = 2030;
        $bankCard->save();

        $response = $this->post('/transaction/save', [
            'name' => 'Other company bank card test',
            'type' => 'expense',
            'amount' => 100,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
            'bank_card_id' => $bankCard->id,
        ]);

        $response->assertSessionHasErrors('bank_card_id');
        $this->assertDatabaseMissing('transaction', ['name' => 'Other company bank card test']);
    }

    #[Test]
    public function it_accepts_customer_belonging_to_own_company(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $response = $this->post('/transaction/save', [
            'name' => 'Own customer test',
            'type' => 'income',
            'amount' => 100,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
            'customer_id' => $customer->id,
        ]);

        $response->assertRedirect('/accounting');
        $this->assertDatabaseHas('transaction', ['name' => 'Own customer test']);
    }

    #[Test]
    public function it_rejects_customer_belonging_to_other_company(): void
    {
        $otherCompany = Company::factory()->create();
        $customer = Customer::factory()->create([
            'company_id' => $otherCompany->id,
        ]);

        $response = $this->post('/transaction/save', [
            'name' => 'Other company customer test',
            'type' => 'income',
            'amount' => 100,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
            'customer_id' => $customer->id,
        ]);

        $response->assertSessionHasErrors('customer_id');
        $this->assertDatabaseMissing('transaction', ['name' => 'Other company customer test']);
    }

    #[Test]
    public function it_accepts_supplier_belonging_to_own_company(): void
    {
        $supplier = Supplier::factory()->create([
            'company_id' => $this->user->company_id,
        ]);

        $response = $this->post('/transaction/save', [
            'name' => 'Own supplier test',
            'type' => 'expense',
            'amount' => 100,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
            'supplier_id' => $supplier->id,
        ]);

        $response->assertRedirect('/accounting');
        $this->assertDatabaseHas('transaction', ['name' => 'Own supplier test']);
    }

    #[Test]
    public function it_rejects_supplier_belonging_to_other_company(): void
    {
        $otherCompany = Company::factory()->create();
        $supplier = Supplier::factory()->create([
            'company_id' => $otherCompany->id,
        ]);

        $response = $this->post('/transaction/save', [
            'name' => 'Other company supplier test',
            'type' => 'expense',
            'amount' => 100,
            'issue_date' => '2025-06-19',
            'status' => 'paid',
            'supplier_id' => $supplier->id,
        ]);

        $response->assertSessionHasErrors('supplier_id');
        $this->assertDatabaseMissing('transaction', ['name' => 'Other company supplier test']);
    }
}
