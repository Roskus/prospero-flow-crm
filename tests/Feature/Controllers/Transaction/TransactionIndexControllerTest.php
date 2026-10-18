<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Transaction;

use App\Models\Company;
use App\Models\Transaction;
use App\Models\Transaction\Category;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionIndexControllerTest extends TestCase
{
    #[Test]
    public function it_paginates_transactions_and_computes_totals_over_all_pages(): void
    {
        $this->createTransactions(12, ['type' => Transaction::INCOME, 'amount' => 100]);
        $this->createTransactions(3, ['type' => Transaction::EXPENSE, 'amount' => 50]);

        $response = $this->get('/accounting');

        $response->assertOk();
        $response->assertViewHas('transactions', fn (LengthAwarePaginator $transactions) => $transactions->count() === 10 && $transactions->total() === 15);
        $response->assertViewHas('totalIncome', 1200.0);
        $response->assertViewHas('totalExpense', 150.0);
        $response->assertSee('id="transaction-pagination"', false);
    }

    #[Test]
    public function it_shows_the_second_page(): void
    {
        $this->createTransactions(12);

        $response = $this->get('/accounting?page=2');

        $response->assertViewHas('transactions', fn (LengthAwarePaginator $transactions) => $transactions->count() === 2 && $transactions->currentPage() === 2);
    }

    #[Test]
    public function it_filters_by_type_and_status_and_totals_follow_the_filters(): void
    {
        $this->createTransactions(2, ['type' => Transaction::EXPENSE, 'status' => Transaction::PAID, 'amount' => 30]);
        $this->createTransactions(1, ['type' => Transaction::EXPENSE, 'status' => Transaction::PENDING, 'amount' => 999]);
        $this->createTransactions(1, ['type' => Transaction::INCOME, 'status' => Transaction::PAID, 'amount' => 500]);

        $response = $this->get('/accounting?type=expense&status=paid');

        $response->assertViewHas('transactions', fn (LengthAwarePaginator $transactions) => $transactions->total() === 2);
        $response->assertViewHas('totalExpense', 60.0);
        $response->assertViewHas('totalIncome', 0.0);
        $response->assertSee(__('Totals for the selected filters'));
    }

    #[Test]
    public function it_filters_by_category(): void
    {
        $rent = Category::create(['company_id' => $this->user->company_id, 'name' => 'Rent']);
        $travel = Category::create(['company_id' => $this->user->company_id, 'name' => 'Travel']);
        $this->createTransactions(2, ['transaction_category_id' => $rent->id]);
        $this->createTransactions(3, ['transaction_category_id' => $travel->id]);

        $response = $this->get('/accounting?transaction_category_id='.$rent->id);

        $response->assertViewHas('transactions', fn (LengthAwarePaginator $transactions) => $transactions->total() === 2);
    }

    #[Test]
    public function it_filters_by_issue_date_range_including_both_ends(): void
    {
        $this->createTransactions(1, ['issue_date' => '2026-09-01']);
        $this->createTransactions(1, ['issue_date' => '2026-09-30']);
        $this->createTransactions(1, ['issue_date' => '2026-08-31']);
        $this->createTransactions(1, ['issue_date' => '2026-10-01']);

        $response = $this->get('/accounting?from=2026-09-01&to=2026-09-30');

        $response->assertViewHas('transactions', fn (LengthAwarePaginator $transactions) => $transactions->total() === 2);
    }

    #[Test]
    public function it_searches_by_name_or_reference(): void
    {
        $this->createTransactions(1, ['name' => 'Office rent 09/2026', 'reference' => null]);
        $this->createTransactions(1, ['name' => 'Invoice order #42', 'reference' => 'INV-42']);
        $this->createTransactions(1, ['name' => 'Train tickets', 'reference' => null]);

        $this->get('/accounting?search=rent')
            ->assertViewHas('transactions', fn (LengthAwarePaginator $transactions) => $transactions->total() === 1);
        $this->get('/accounting?search=INV-42')
            ->assertViewHas('transactions', fn (LengthAwarePaginator $transactions) => $transactions->total() === 1);
    }

    #[Test]
    public function search_does_not_leak_transactions_of_other_companies(): void
    {
        $this->createTransactions(1, ['name' => 'Own expense', 'reference' => 'ZZREF-1']);
        Transaction::factory()->create(['company_id' => Company::factory()->create()->id, 'name' => 'Foreign expense', 'reference' => 'ZZREF-2']);

        $response = $this->get('/accounting?search=ZZREF');

        $response->assertViewHas('transactions', fn (LengthAwarePaginator $transactions) => $transactions->total() === 1);
        $response->assertDontSee('Foreign expense');
    }

    #[Test]
    public function pagination_links_keep_the_filters(): void
    {
        $this->createTransactions(12, ['type' => Transaction::EXPENSE]);

        $response = $this->get('/accounting?type=expense');

        $response->assertSee('type=expense&amp;page=2', false);
    }

    #[Test]
    public function it_rejects_invalid_filters(): void
    {
        $foreignCategory = Category::create(['company_id' => Company::factory()->create()->id, 'name' => 'Foreign']);

        $response = $this->from('/accounting')->get('/accounting?type=gift&status=lost&transaction_category_id='.$foreignCategory->id.'&from=2026-10-10&to=2026-10-01');

        $response->assertRedirect('/accounting');
        $response->assertSessionHasErrors([
            'type' => __('The selected type is not valid.'),
            'status' => __('The selected status is not valid.'),
            'transaction_category_id' => __('The selected category is not valid.'),
            'to' => __('The end date must be on or after the start date.'),
        ]);
    }

    #[Test]
    public function user_without_read_transaction_permission_is_forbidden(): void
    {
        $user = User::factory()->create();
        $user->assignRole('User');

        $this->actingAs($user)->get('/accounting')->assertForbidden();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createTransactions(int $count, array $attributes = []): void
    {
        Transaction::factory()->count($count)->create(array_merge([
            'company_id' => $this->user->company_id,
            'issue_date' => '2026-10-01',
        ], $attributes));
    }
}
