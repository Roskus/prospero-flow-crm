<?php

declare(strict_types=1);

namespace App\Models\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

final class Item extends Model
{
    protected $table = 'order_item';

    private ?int $order_id;

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
