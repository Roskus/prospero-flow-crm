<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Order amounts were calculated from the items of every order sharing the order number across companies,
 * and before the new items were saved. Orders with items get the total of their own items again;
 * orders without items keep the amount they were created with.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('order_item')
            ->select('order_id')
            ->selectRaw('SUM(unit_price * quantity * (1 - COALESCE(discount, 0) / 100)) AS total')
            ->groupBy('order_id')
            ->orderBy('order_id')
            ->chunk(500, function ($totals): void {
                foreach ($totals as $total) {
                    DB::table('order')->where('id', $total->order_id)->update(['amount' => $total->total]);
                }
            });
    }

    /**
     * Not reversible: the previous amounts included items of other companies and are not kept anywhere,
     * so rolling back leaves the recalculated amounts in place.
     */
    public function down(): void {}
};
