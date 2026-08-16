<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Company;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    public function creating(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            Company::where('id', $order->getCompanyId())
                ->lockForUpdate()
                ->increment('last_order_number');

            $order->order_number = Company::where('id', $order->getCompanyId())
                ->value('last_order_number');
        });
    }
}
