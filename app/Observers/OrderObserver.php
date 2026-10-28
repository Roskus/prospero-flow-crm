<?php

declare(strict_types=1);

namespace App\Observers;

use App\Exceptions\InvoicedOrderException;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Order;
use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderObserver
{
    public function __construct(private ExchangeRateService $exchangeRateService) {}

    /**
     * Orders default to the company currency and keep the rate of the moment their currency is set.
     *
     * @throws ValidationException when the company has no rate for the order currency
     */
    public function saving(Order $order): void
    {
        $company = Company::withTrashed()->find($order->getCompanyId());

        if ($company === null) {
            return;
        }

        $order->currency = strtoupper((string) ($order->currency ?: $company->currency));

        if (! $order->exists || $order->isDirty('currency')) {
            $order->exchange_rate = $this->exchangeRateService->rate($company, $order->currency);
        }
    }

    /**
     * Safety net for code paths without their own check: an invoiced order only takes amount refreshes
     * and being completed by its invoice.
     *
     * @throws InvoicedOrderException
     */
    public function updating(Order $order): void
    {
        $changed = array_keys($order->getDirty());
        $allowed = ['amount', 'updated_at', 'updated_by'];

        if ($order->isDirty('status') && (int) $order->status === Order::COMPLETED) {
            $allowed[] = 'status';
        }

        if (array_diff($changed, $allowed) !== [] && $order->isInvoiced()) {
            throw InvoicedOrderException::for($order);
        }
    }

    /**
     * @throws InvoicedOrderException
     */
    public function deleting(Order $order): void
    {
        if ($order->isInvoiced()) {
            throw InvoicedOrderException::for($order);
        }
    }

    public function creating(Order $order): void
    {
        $order->branch_id ??= $this->defaultBranchId($order);

        DB::transaction(function () use ($order): void {
            Company::withTrashed()
                ->where('id', $order->getCompanyId())
                ->lockForUpdate()
                ->increment('last_order_number');

            $order->order_number = Company::withTrashed()
                ->where('id', $order->getCompanyId())
                ->value('last_order_number');
        });
    }

    /**
     * The creator's current branch when it belongs to the order company, otherwise the company's main branch
     * (seeders, commands, orders created for another company).
     */
    private function defaultBranchId(Order $order): ?int
    {
        $currentBranch = Auth::user()?->currentBranch();

        if ($currentBranch !== null && $currentBranch->company_id === $order->getCompanyId()) {
            return $currentBranch->id;
        }

        return Branch::ofCompany($order->getCompanyId())->where('is_main', true)->value('id');
    }
}
