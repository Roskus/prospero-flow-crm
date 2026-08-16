<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Assign sequential order numbers to orders that have a null order_number,
     * keeping them unique per company, and sync the referenced order items and
     * the company's last_order_number counter.
     */
    public function up(): void
    {
        $companyIds = DB::table('order')
            ->whereNull('order_number')
            ->distinct()
            ->pluck('company_id');

        foreach ($companyIds as $companyId) {
            DB::transaction(function () use ($companyId): void {
                $counter = (int) DB::table('company')
                    ->where('id', $companyId)
                    ->lockForUpdate()
                    ->value('last_order_number');

                $storedMax = (int) DB::table('order')
                    ->where('company_id', $companyId)
                    ->whereNotNull('order_number')
                    ->max('order_number');

                $next = max($counter, $storedMax);

                $orderIds = DB::table('order')
                    ->where('company_id', $companyId)
                    ->whereNull('order_number')
                    ->orderBy('id')
                    ->pluck('id');

                foreach ($orderIds as $orderId) {
                    $next++;

                    while (DB::table('order')
                        ->where('company_id', $companyId)
                        ->where('order_number', $next)
                        ->exists()) {
                        $next++;
                    }

                    DB::table('order')->where('id', $orderId)->update(['order_number' => $next]);

                    DB::table('order_item')->where('order_id', $orderId)->update(['order_number' => $next]);
                }

                DB::table('company')
                    ->where('id', $companyId)
                    ->where('last_order_number', '<', $next)
                    ->update(['last_order_number' => $next]);
            });
        }
    }

    public function down(): void
    {
        // Intentionally a no-op: order numbers cannot be reliably un-assigned.
    }
};
