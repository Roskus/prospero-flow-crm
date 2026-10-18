<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\Transaction\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Demo data for the sales report and accounting: orders (sales) and income/expense
 * transactions spread over the last 3 months. Not part of DatabaseSeeder; run it manually:
 *
 *   php artisan db:seed --class=SalesAndExpensesDemoSeeder
 */
class SalesAndExpensesDemoSeeder extends Seeder
{
    private const int ORDER_COUNT = 45;

    private const int RANDOM_EXPENSE_COUNT = 15;

    private const int TAX_PERCENTAGE = 21;

    /**
     * Unit prices outside this range (e.g. factory generated products) are replaced by a realistic one
     * so the demo amounts look like a small business.
     */
    private const float MIN_UNIT_PRICE = 1.0;

    private const float MAX_UNIT_PRICE = 500.0;

    /**
     * Order status => weight.
     */
    private const array ORDER_STATUS_WEIGHTS = [
        Order::COMPLETED => 45,
        Order::CONFIRMED => 30,
        Order::PENDING => 15,
        Order::CANCELED => 10,
    ];

    /**
     * Monthly fixed expenses: category => [name, min amount, max amount].
     */
    private const array MONTHLY_EXPENSES = [
        'Rent' => ['Office rent', 1200, 1500],
        'Salaries' => ['Payroll', 8000, 12000],
        'Utilities' => ['Electricity and internet', 180, 420],
    ];

    /**
     * Occasional expenses: category => [names, min amount, max amount].
     */
    private const array RANDOM_EXPENSES = [
        'Marketing' => [['Online ads campaign', 'Trade fair stand', 'Printed brochures'], 150, 2500],
        'Travel' => [['Client visit trip', 'Train tickets', 'Hotel stay'], 60, 900],
        'Office supplies' => [['Stationery', 'Printer toner', 'Office chairs'], 25, 600],
        'Services' => [['Accounting services', 'IT support', 'Legal advice'], 120, 1500],
    ];

    public function run(?int $companyId = null): void
    {
        $company = $this->resolveCompany($companyId);
        $from = Carbon::now()->subMonthsNoOverflow(3)->startOfDay();
        $to = Carbon::now();

        $summary = DB::transaction(function () use ($company, $from, $to): array {
            $categories = $this->ensureCategories($company);
            $sellers = User::where('company_id', $company->id)->get();
            $customers = $this->ensureCustomers($company, $sellers);
            $suppliers = $this->ensureSuppliers($company);
            $products = $this->ensureProducts($company);

            $orders = $this->createOrders($company, $sellers, $customers, $products, $from, $to);
            $incomes = $this->createIncomes($company, $orders, $categories['Sales']);
            $expenses = $this->createMonthlyExpenses($company, $categories, $from, $to)
                + $this->createRandomExpenses($company, $categories, $suppliers, $from, $to);

            return ['orders' => $orders->count(), 'incomes' => $incomes, 'expenses' => $expenses];
        });

        $this->command?->info(sprintf(
            'Company "%s" (#%d): %d orders, %d income and %d expense transactions between %s and %s.',
            $company->name,
            $company->id,
            $summary['orders'],
            $summary['incomes'],
            $summary['expenses'],
            $from->toDateString(),
            $to->toDateString(),
        ));
    }

    private function resolveCompany(?int $companyId): Company
    {
        if ($companyId === null && $this->command !== null && ! $this->command->option('no-interaction')) {
            $defaultCompanyId = Company::find(Company::DEFAULT_COMPANY)?->id ?? Company::query()->value('id');
            $companyId = (int) $this->command->ask('Company id to seed', (string) $defaultCompanyId);
        }

        return $companyId === null
            ? (Company::find(Company::DEFAULT_COMPANY) ?? Company::query()->firstOrFail())
            : Company::findOrFail($companyId);
    }

    /**
     * @return array<string, Category>
     */
    private function ensureCategories(Company $company): array
    {
        $names = array_merge(['Sales'], array_keys(self::MONTHLY_EXPENSES), array_keys(self::RANDOM_EXPENSES));

        return collect($names)
            ->mapWithKeys(fn (string $name) => [$name => Category::firstOrCreate(['company_id' => $company->id, 'name' => $name])])
            ->all();
    }

    /**
     * @param  Collection<int, User>  $sellers
     * @return Collection<int, Customer>
     */
    private function ensureCustomers(Company $company, Collection $sellers): Collection
    {
        $customers = Customer::where('company_id', $company->id)->get();

        if ($customers->count() >= 5) {
            return $customers;
        }

        return $customers->merge(
            Customer::factory()->count(8)->create([
                'company_id' => $company->id,
                'seller_id' => fn () => $sellers->random()->id,
            ])
        );
    }

    /**
     * @return Collection<int, Supplier>
     */
    private function ensureSuppliers(Company $company): Collection
    {
        $suppliers = Supplier::where('company_id', $company->id)->get();

        if ($suppliers->count() >= 3) {
            return $suppliers;
        }

        return $suppliers->merge(Supplier::factory()->count(5)->create(['company_id' => $company->id]));
    }

    /**
     * @return Collection<int, Product>
     */
    private function ensureProducts(Company $company): Collection
    {
        $products = Product::where('company_id', $company->id)->get();

        if ($products->count() >= 5) {
            return $products;
        }

        return $products->merge(
            Product::factory()->count(10)->create([
                'company_id' => $company->id,
                'category_id' => null,
                'brand_id' => null,
                'photo' => null,
                'price' => fn () => fake()->randomFloat(2, 15, 450),
            ])
        );
    }

    /**
     * @param  Collection<int, User>  $sellers
     * @param  Collection<int, Customer>  $customers
     * @param  Collection<int, Product>  $products
     * @return Collection<int, Order>
     */
    private function createOrders(Company $company, Collection $sellers, Collection $customers, Collection $products, Carbon $from, Carbon $to): Collection
    {
        $orders = new Collection;

        for ($index = 0; $index < self::ORDER_COUNT; $index++) {
            $createdAt = Carbon::createFromTimestamp(fake()->numberBetween($from->timestamp, $to->timestamp));

            $order = Order::factory()->create([
                'company_id' => $company->id,
                'customer_id' => $customers->random()->id,
                'seller_id' => $sellers->random()->id,
                'currency' => $company->currency ?? 'EUR',
                'status' => $this->randomOrderStatus(),
                'amount' => 0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $amount = 0.0;
            foreach ($products->random(min($products->count(), fake()->numberBetween(1, 4))) as $product) {
                $quantity = fake()->numberBetween(1, 10);
                $unitPrice = $this->unitPrice($product);
                $discount = fake()->randomElement([0, 0, 0, 5, 10]);

                Item::factory()->create([
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'tax' => self::TAX_PERCENTAGE,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $amount += $unitPrice * $quantity * (1 - $discount / 100);
            }

            $order->amount = round($amount, 2);
            $order->timestamps = false;
            $order->save();

            $orders->push($order);
        }

        return $orders;
    }

    /**
     * Completed orders are invoiced and paid; confirmed orders are invoiced and pending or overdue.
     *
     * @param  Collection<int, Order>  $orders
     */
    private function createIncomes(Company $company, Collection $orders, Category $salesCategory): int
    {
        $invoicedOrders = $orders->filter(fn (Order $order) => in_array((int) $order->status, [Order::COMPLETED, Order::CONFIRMED], true));

        foreach ($invoicedOrders as $order) {
            $issueDate = $order->created_at->copy();
            $dueDate = $issueDate->copy()->addDays(30);
            $isPaid = (int) $order->status === Order::COMPLETED;

            Transaction::factory()->create([
                'company_id' => $company->id,
                'type' => Transaction::INCOME,
                'name' => 'Invoice order #'.$order->order_number,
                'reference' => 'INV-'.$order->order_number,
                'transaction_category_id' => $salesCategory->id,
                'customer_id' => $order->customer_id,
                'amount' => round((float) $order->amount * (1 + self::TAX_PERCENTAGE / 100), 2),
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'payment_date' => $isPaid ? $issueDate->copy()->addDays(fake()->numberBetween(0, 20)) : null,
                'status' => $isPaid ? Transaction::PAID : ($dueDate->isPast() ? Transaction::OVERDUE : Transaction::PENDING),
                'notes' => null,
                'created_at' => $issueDate,
                'updated_at' => $issueDate,
            ]);
        }

        return $invoicedOrders->count();
    }

    /**
     * @param  array<string, Category>  $categories
     */
    private function createMonthlyExpenses(Company $company, array $categories, Carbon $from, Carbon $to): int
    {
        $count = 0;

        for ($month = $from->copy()->startOfMonth(); $month->lte($to); $month->addMonthNoOverflow()) {
            $issueDate = $month->copy()->day(1);
            if ($issueDate->lt($from) || $issueDate->gt($to)) {
                continue;
            }

            foreach (self::MONTHLY_EXPENSES as $categoryName => [$name, $min, $max]) {
                $this->createExpense($company, $categories[$categoryName], $name.' '.$issueDate->format('m/Y'), fake()->randomFloat(2, $min, $max), $issueDate);
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param  array<string, Category>  $categories
     * @param  Collection<int, Supplier>  $suppliers
     */
    private function createRandomExpenses(Company $company, array $categories, Collection $suppliers, Carbon $from, Carbon $to): int
    {
        for ($index = 0; $index < self::RANDOM_EXPENSE_COUNT; $index++) {
            $categoryName = fake()->randomElement(array_keys(self::RANDOM_EXPENSES));
            [$names, $min, $max] = self::RANDOM_EXPENSES[$categoryName];
            $issueDate = Carbon::createFromTimestamp(fake()->numberBetween($from->timestamp, $to->timestamp))->startOfDay();

            $this->createExpense($company, $categories[$categoryName], fake()->randomElement($names), fake()->randomFloat(2, $min, $max), $issueDate, $suppliers->random());
        }

        return self::RANDOM_EXPENSE_COUNT;
    }

    private function createExpense(Company $company, Category $category, string $name, float $amount, Carbon $issueDate, ?Supplier $supplier = null): void
    {
        $dueDate = $issueDate->copy()->addDays(15);
        $isPaid = fake()->boolean(85);

        Transaction::factory()->create([
            'company_id' => $company->id,
            'type' => Transaction::EXPENSE,
            'name' => $name,
            'reference' => null,
            'transaction_category_id' => $category->id,
            'supplier_id' => $supplier?->id,
            'amount' => $amount,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'payment_date' => $isPaid ? $issueDate->copy()->addDays(fake()->numberBetween(0, 10)) : null,
            'status' => $isPaid ? Transaction::PAID : ($dueDate->isPast() ? Transaction::OVERDUE : Transaction::PENDING),
            'notes' => null,
            'created_at' => $issueDate,
            'updated_at' => $issueDate,
        ]);
    }

    private function unitPrice(Product $product): float
    {
        $price = (float) $product->price;

        return $price >= self::MIN_UNIT_PRICE && $price <= self::MAX_UNIT_PRICE
            ? $price
            : fake()->randomFloat(2, 15, 450);
    }

    private function randomOrderStatus(): int
    {
        $roll = fake()->numberBetween(1, array_sum(self::ORDER_STATUS_WEIGHTS));

        foreach (self::ORDER_STATUS_WEIGHTS as $status => $weight) {
            $roll -= $weight;
            if ($roll <= 0) {
                return $status;
            }
        }

        return Order::COMPLETED;
    }
}
