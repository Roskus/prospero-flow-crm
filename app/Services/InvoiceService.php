<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\ValueObjects\Address;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public const int DEFAULT_DUE_DAYS = 30;

    public function __construct(private ExchangeRateService $exchangeRateService) {}

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
            $customer = Customer::query()->where('company_id', $order->getCompanyId())->findOrFail($order->getCustomerId());
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
     * Subtotal (quantity × unit price minus the discount), tax and total of a line, each rounded to 2 decimals.
     *
     * @return array{subtotal: string, tax_amount: string, total: string}
     */
    public static function lineTotals(string $quantity, string $unitPrice, string $discount, string $taxPercentage): array
    {
        $gross = bcmul($quantity, $unitPrice, 10);
        $subtotal = ExchangeRateService::round(bcsub($gross, bcdiv(bcmul($gross, $discount, 10), '100', 10), 10));
        $taxAmount = ExchangeRateService::round(bcdiv(bcmul($subtotal, $taxPercentage, 10), '100', 10));

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => bcadd($subtotal, $taxAmount, 2),
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
