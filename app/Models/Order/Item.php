<?php

declare(strict_types=1);

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

final class Item extends Model
{
    protected $table = 'order_item';

    private ?int $order_id;

}
