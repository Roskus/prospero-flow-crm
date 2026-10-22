<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
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
