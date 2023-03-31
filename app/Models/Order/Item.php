<?php

declare(strict_types=1);

namespace App\Models\Order;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 *  @OA\Schema(
 *    schema="OrderItem",
 *    type="object",
 *    required={"product_id", "quantity", "unit_price"},
 *    @OA\Property(
 *        property="product_id",
 *        description="Product ID of the Item",
 *        type="int",
 *        example="1"
 *    ),
 *    @OA\Property(
 *        property="quantity",
 *        description="Quantity of the Item",
 *        type="int",
 *        example="3"
 *    ),
 *    @OA\Property(
 *        property="unit_price",
 *        description="Unit Price of the Item",
 *        type="double",
 *        example="3.5"
 *    ),
 *    @OA\Property(
 *        property="discount",
 *        description="Discount % of the Item",
 *        type="double",
 *        example="10.0"
 *    ),
 *    @OA\Property(
 *        property="tax",
 *        description="Tax % of the Item",
 *        type="double",
 *        example="21.0"
 *    )
 *  )
 */
final class Item extends Model
{
    use HasFactory;

    protected $table = 'order_item';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount',
        'tax',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    private ?int $order_id;

    protected $with = ['product'];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function getSubTotal()
    {
        $amount = $this->unit_price * $this->quantity;

        return $amount - (($this->discount / 100) * $amount);
    }
}
