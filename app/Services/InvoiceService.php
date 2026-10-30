<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\CurrencyHelper;
use App\Models\Bank\Account;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Transaction\Category;
use App\ValueObjects\Address;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public const int DEFAULT_DUE_DAYS = 30;

    public function __construct(
        private ExchangeRateService $exchangeRateService,
        private CashAccountService $cashAccountService,
    ) {}

    /**
     * Issues the invoice of a confirmed order: copies its lines, numbers it in the order branch,
     * snapshots the printed data and completes the order.
     *
     * @param  array{issue_date?: string|null, due_date?: string|null, exchange_rate?: string|float|int|null, notes?: string|null}  $data
     *
     * @throws ValidationException when the order cannot be invoiced
     */
    public function issue(Order $order, array $data = []): Invoice
    {
        return DB::transaction(function () use ($order, $data): Invoice {
            $order = Order::query()->whereKey($order->getKey())->lockForUpdate()->firstOrFail();
            $this->ensureCanBeInvoiced($order);

            $branch = Branch::withTrashed()->whereKey($order->branch_id)->lockForUpdate()->firstOrFail();
            $branch->increment('last_invoice_number');
            $number = (int) Branch::withTrashed()->whereKey($branch->id)->value('last_invoice_number');

            $company = Company::withTrashed()->findOrFail($order->getCompanyId());
            $customer = Customer::withTrashed()
                ->where('company_id', $order->getCompanyId())
                ->findOrFail($order->getCustomerId());
            $issueDate = $data['issue_date'] ?? today()->toDateString();

            $invoice = Invoice::create([
                'company_id' => $order->getCompanyId(),
                'branch_id' => $branch->id,
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'series' => $branch->code,
                'number' => $number,
                'issue_date' => $issueDate,
                'due_date' => $data['due_date'] ?? Carbon::parse($issueDate)->addDays(self::DEFAULT_DUE_DAYS)->toDateString(),
                'currency' => $order->currency,
                'exchange_rate' => $this->exchangeRate($company, $order->currency, $data['exchange_rate'] ?? null),
                'status' => Invoice::ISSUED,
                'customer_data' => $this->customerSnapshot($customer),
                'issuer_data' => $this->issuerSnapshot($company, $branch),
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $subtotal = $tax = '0';
            foreach ($order->load(['items.product' => fn (HasOne $query) => $query->withTrashed()])->items as $orderItem) {
                $line = self::lineTotals((string) $orderItem->quantity, (string) $orderItem->unit_price, (string) ($orderItem->discount ?? 0), (string) ($orderItem->tax ?? 0));
                $invoice->items()->create([
                    'product_id' => $orderItem->product_id,
                    'description' => (string) ($orderItem->product->name ?? ''),
                    'sku' => $orderItem->product->sku ?? null,
                    'barcode' => $orderItem->product->barcode ?? null,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'discount' => $orderItem->discount ?? 0,
                    'tax' => $orderItem->tax ?? 0,
                    ...$line,
                ]);
                $subtotal = bcadd($subtotal, $line['subtotal'], 2);
                $tax = bcadd($tax, $line['tax_amount'], 2);
            }

            $invoice->update(['subtotal' => $subtotal, 'tax' => $tax, 'total' => bcadd($subtotal, $tax, 2)]);

            $order->status = Order::COMPLETED;
            $order->save();

            return $invoice;
        });
    }

    /**
     * Cancels an invoice without payments; it keeps its number and its order can be invoiced again.
     *
     * @throws ValidationException when the invoice has payments or is already cancelled
     */
    public function cancel(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice): Invoice {
            $invoice = Invoice::query()->whereKey($invoice->getKey())->lockForUpdate()->firstOrFail();

            if ($invoice->isCancelled()) {
                throw ValidationException::withMessages(['invoice' => __('The invoice is already cancelled')]);
            }

            if ($invoice->hasPayments()) {
                throw ValidationException::withMessages(['invoice' => __('Delete the payments of the invoice before cancelling it')]);
            }

            $invoice->update(['status' => Invoice::CANCELLED, 'cancelled_at' => now()]);

            $order = Order::query()->whereKey($invoice->order_id)->lockForUpdate()->first();
            if ($order !== null && (int) $order->status === Order::COMPLETED) {
                $order->status = Order::CONFIRMED;
                $order->save();
            }

            return $invoice;
        });
    }

    /**
     * Registers a payment of the invoice as a paid income transaction in the sales category and refreshes its payment status.
     *
     * @param  array{amount: string|float|int, payment_method: string, bank_account_id?: int|string|null, currency?: string|null, exchange_rate?: string|float|int|null, payment_date?: string|null, reference?: string|null, attachment?: UploadedFile|null}  $data
     *
     * @throws ValidationException when the payment breaks a payment rule
     * @throws ModelNotFoundException when the account does not belong to the invoice company
     */
    public function registerPayment(Invoice $invoice, array $data): Transaction
    {
        return DB::transaction(function () use ($invoice, $data): Transaction {
            $invoice = Invoice::query()->whereKey($invoice->getKey())->lockForUpdate()->firstOrFail();

            if ($invoice->isCancelled()) {
                throw ValidationException::withMessages(['invoice' => __('A cancelled invoice cannot receive payments')]);
            }

            $method = (string) $data['payment_method'];
            if (! array_key_exists($method, Transaction::PAYMENT_METHODS)) {
                throw ValidationException::withMessages(['payment_method' => __('The payment method is not valid')]);
            }

            $account = $this->paymentAccount($invoice, $method, $data['bank_account_id'] ?? null);
            $company = Company::withTrashed()->findOrFail($invoice->company_id);
            $currency = strtoupper((string) ($data['currency'] ?? '') ?: $invoice->currency);
            $exchangeRate = $this->exchangeRate($company, $currency, $data['exchange_rate'] ?? null);
            $amount = ExchangeRateService::round((string) $data['amount']);
            $settled = self::settledAmount($invoice, $amount, $currency, $exchangeRate);

            if (bccomp($settled, '0', 2) <= 0) {
                throw ValidationException::withMessages(['amount' => __('The payment amount must be greater than zero')]);
            }

            $balance = $invoice->balance();
            if (bccomp($settled, $balance, 2) > 0) {
                throw ValidationException::withMessages(['amount' => __('The payment exceeds the balance of the invoice, which is :balance', ['balance' => CurrencyHelper::formatIn($balance, $invoice->currency)])]);
            }

            $paymentDate = $data['payment_date'] ?? today()->toDateString();
            $transaction = new Transaction([
                'company_id' => $invoice->company_id,
                'issue_date' => $paymentDate,
                'payment_date' => $paymentDate,
                'name' => mb_substr(__('Payment of invoice :number', ['number' => $invoice->fullNumber()]), 0, 80),
                'type' => Transaction::INCOME,
                'status' => Transaction::PAID,
                'transaction_category_id' => Category::firstOrCreate(['company_id' => $invoice->company_id, 'name' => Category::SALES])->id,
                'bank_account_id' => $account?->id,
                'customer_id' => $invoice->customer_id,
                'amount' => $amount,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
                'payment_method' => $method,
                'reference' => $data['reference'] ?? null,
            ]);
            $transaction->invoice_id = $invoice->id;

            if (($data['attachment'] ?? null) instanceof UploadedFile) {
                $transaction->attachment = $data['attachment']->store('accounting/'.$invoice->company_id, 'public');
            }

            $transaction->save();

            return $transaction;
        });
    }

    /**
     * Paid amount and status of the invoice from its paid payments: issued, partially paid or paid.
     * A cancelled invoice keeps its status. The invoice row is locked so concurrent payment changes
     * refresh it one after the other and the last one sees every committed payment.
     */
    public function refreshPaymentStatus(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice): void {
            $locked = Invoice::query()->whereKey($invoice->getKey())->lockForUpdate()->firstOrFail();
            $paid = ExchangeRateService::round((string) $locked->payments()->where('status', Transaction::PAID)->sum('invoice_amount'));

            $locked->paid_amount = $paid;

            if (! $locked->isCancelled()) {
                $locked->status = match (true) {
                    bccomp($paid, '0', 2) <= 0 => Invoice::ISSUED,
                    bccomp($paid, (string) $locked->total, 2) >= 0 => Invoice::PAID,
                    default => Invoice::PARTIALLY_PAID,
                };
            }

            $locked->save();
            $invoice->setRawAttributes($locked->getAttributes(), true);
        });
    }

    /**
     * Deletes a payment with its invoice locked, so the paid amount and status are refreshed in the same transaction.
     */
    public function deletePayment(Transaction $payment): void
    {
        DB::transaction(function () use ($payment): void {
            Invoice::query()->whereKey($payment->invoice_id)->lockForUpdate()->firstOrFail();
            $payment->delete();
        });
    }

    /**
     * Transfers and Bizum go to a chosen account; cash goes to the branch cash account when none is chosen.
     *
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    private function paymentAccount(Invoice $invoice, string $method, int|string|null $accountId): ?Account
    {
        if (! empty($accountId)) {
            return Account::query()->where('company_id', $invoice->company_id)->findOrFail((int) $accountId);
        }

        if (in_array($method, Transaction::METHODS_REQUIRING_ACCOUNT, true)) {
            throw ValidationException::withMessages(['bank_account_id' => __('Choose the account that receives the payment')]);
        }

        if ($method === Transaction::METHOD_CASH) {
            return $this->cashAccountService->createForBranch(Branch::withTrashed()->findOrFail($invoice->branch_id));
        }

        return null;
    }

    /**
     * Amount of a payment in the invoice currency: converted through the company currency with the payment
     * rate and the invoice rate, rounded to 2 decimals.
     */
    public static function settledAmount(Invoice $invoice, string $amount, string $currency, string $exchangeRate): string
    {
        if (strtoupper($currency) === strtoupper($invoice->currency)) {
            return ExchangeRateService::round($amount);
        }

        $inCompanyCurrency = bcmul($amount, $exchangeRate, 10);

        return ExchangeRateService::round(bcdiv($inCompanyCurrency, (string) $invoice->exchange_rate, 10));
    }

    /**
     * Unit prices include taxes, so the total of a line is quantity × unit price minus the discount and the tax is
     * broken down from it: subtotal = total / (1 + tax%), tax = total - subtotal. Each amount is rounded to 2 decimals.
     *
     * @return array{subtotal: string, tax_amount: string, total: string}
     */
    public static function lineTotals(string $quantity, string $unitPrice, string $discount, string $taxPercentage): array
    {
        $gross = bcmul($quantity, $unitPrice, 10);
        $total = ExchangeRateService::round(bcsub($gross, bcdiv(bcmul($gross, $discount, 10), '100', 10), 10));
        $subtotal = ExchangeRateService::round(bcdiv(bcmul($total, '100', 10), bcadd('100', $taxPercentage, 10), 10));

        return [
            'subtotal' => $subtotal,
            'tax_amount' => bcsub($total, $subtotal, 2),
            'total' => $total,
        ];
    }

    /**
     * @throws ValidationException
     */
    private function ensureCanBeInvoiced(Order $order): void
    {
        if (! in_array((int) $order->status, [Order::CONFIRMED, Order::COMPLETED], true)) {
            throw ValidationException::withMessages(['order' => __('Only confirmed or completed orders can be invoiced')]);
        }

        if ($order->isInvoiced()) {
            throw ValidationException::withMessages(['order' => __('The order already has the invoice :number', ['number' => $order->activeInvoice->fullNumber()])]);
        }

        if (! $order->items()->exists()) {
            throw ValidationException::withMessages(['order' => __('The order has no lines to invoice')]);
        }
    }

    private function exchangeRate(Company $company, string $currency, string|float|int|null $rate): string
    {
        if (strtoupper($currency) === strtoupper((string) $company->currency)) {
            return '1.000000';
        }

        if ($rate !== null && $rate !== '') {
            return bcadd((string) $rate, '0', 6);
        }

        return $this->exchangeRateService->rate($company, $currency, 'exchange_rate');
    }

    /**
     * @return array{name: ?string, business_name: ?string, vat: ?string, email: ?string, phone: ?string, address: array<string, mixed>}
     */
    private function customerSnapshot(Customer $customer): array
    {
        return [
            'name' => $customer->name,
            'business_name' => $customer->business_name,
            'vat' => $customer->vat,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => Address::fromArray($customer->only(Address::COLUMNS))->toArray(),
        ];
    }

    /**
     * @return array{company: array<string, mixed>, branch: array<string, mixed>}
     */
    private function issuerSnapshot(Company $company, Branch $branch): array
    {
        return [
            'company' => [
                'name' => $company->name,
                'business_name' => $company->business_name,
                'vat' => $company->vat,
                'registration_number' => $company->registration_number,
                'phone' => $company->phone,
                'email' => $company->email,
                'website' => $company->website,
                'currency' => strtoupper((string) $company->currency),
                'address' => $company->address->toArray(),
            ],
            'branch' => [
                'code' => $branch->code,
                'name' => $branch->name,
                'phone' => $branch->phone,
                'email' => $branch->email,
                'address' => $branch->address->toArray(),
            ],
        ];
    }
}
