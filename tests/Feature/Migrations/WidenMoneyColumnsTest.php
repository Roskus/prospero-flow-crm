<?php

declare(strict_types=1);

namespace Tests\Feature\Migrations;

use App\Helpers\CurrencyHelper;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WidenMoneyColumnsTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function moneyColumns(): array
    {
        return [
            'transaction.amount' => ['transaction', 'amount'],
            'order.amount' => ['order', 'amount'],
            'order_item.unit_price' => ['order_item', 'unit_price'],
            'product.cost' => ['product', 'cost'],
            'product.price' => ['product', 'price'],
            'bank_account.opening_balance' => ['bank_account', 'opening_balance'],
            'payroll.gross_amount' => ['payroll', 'gross_amount'],
            'payroll.net_amount' => ['payroll', 'net_amount'],
        ];
    }

    #[Test]
    #[DataProvider('moneyColumns')]
    public function money_columns_are_decimal_18_5(string $table, string $column): void
    {
        $columnType = DB::selectOne(
            'SELECT COLUMN_TYPE AS type FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        )->type;

        $this->assertSame('decimal(18,5)', $columnType);
    }

    #[Test]
    public function original_nullability_and_defaults_are_kept(): void
    {
        $columns = collect(DB::select(
            "SELECT TABLE_NAME AS t, COLUMN_NAME AS c, IS_NULLABLE AS n, COLUMN_DEFAULT AS d FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('order_item', 'payroll', 'bank_account', 'transaction')
             AND COLUMN_NAME IN ('unit_price', 'gross_amount', 'net_amount', 'opening_balance', 'amount')"
        ))->keyBy(fn (object $column) => $column->t.'.'.$column->c);

        $this->assertSame('YES', $columns['order_item.unit_price']->n);
        $this->assertSame('YES', $columns['payroll.gross_amount']->n);
        $this->assertSame('NO', $columns['payroll.net_amount']->n);
        $this->assertSame('NO', $columns['transaction.amount']->n);
        $this->assertSame('NO', $columns['bank_account.opening_balance']->n);
        $this->assertEquals(0, (float) $columns['bank_account.opening_balance']->d);
    }

    #[Test]
    public function it_stores_amounts_of_hundreds_of_millions_exactly(): void
    {
        $transaction = Transaction::factory()->create([
            'company_id' => $this->user->company_id,
            'amount' => '358750000.12345',
        ]);
        $product = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'photo' => null,
            'price' => '125000000.5',
            'cost' => '99000000',
        ]);

        $this->assertSame('358750000.12345', DB::table('transaction')->where('id', $transaction->id)->value('amount'));
        $this->assertSame('125000000.50000', DB::table('product')->where('id', $product->id)->value('price'));
    }

    #[Test]
    public function it_stores_the_maximum_allowed_amount(): void
    {
        $transaction = Transaction::factory()->create([
            'company_id' => $this->user->company_id,
            'amount' => CurrencyHelper::MAX_AMOUNT,
        ]);

        $this->assertSame(CurrencyHelper::MAX_AMOUNT, DB::table('transaction')->where('id', $transaction->id)->value('amount'));
    }

    #[Test]
    public function web_transaction_form_accepts_millions_and_rejects_amounts_beyond_the_limit(): void
    {
        $payload = [
            'name' => 'Venta ARS',
            'type' => 'income',
            'issue_date' => '2026-10-18',
            'status' => 'paid',
        ];

        $this->post('/transaction/save', $payload + ['amount' => '250000000.75'])
            ->assertRedirect('/accounting');
        $this->assertSame('250000000.75000', DB::table('transaction')->where('name', 'Venta ARS')->value('amount'));

        $this->from('/transaction/create')
            ->post('/transaction/save', $payload + ['amount' => '10000000000000'])
            ->assertSessionHasErrors('amount');

        $this->from('/transaction/create')
            ->post('/transaction/save', $payload + ['amount' => '-10000000000000'])
            ->assertSessionHasErrors('amount');
    }

    #[Test]
    public function api_rejects_amounts_beyond_the_limit_with_a_validation_error(): void
    {
        $response = $this->actingAs($this->user, 'api')->postJson('/api/transaction', [
            'name' => 'Too big',
            'type' => 'income',
            'amount' => '10000000000000',
            'issue_date' => '2026-10-18',
            'status' => 'paid',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('amount');
    }

    #[Test]
    public function product_form_rejects_prices_beyond_the_limit(): void
    {
        $response = $this->from('/product/create')->post('/product/save', [
            'name' => 'Huge',
            'price' => '10000000000000',
            'cost' => '1',
        ]);

        $response->assertSessionHasErrors('price');
    }
}
