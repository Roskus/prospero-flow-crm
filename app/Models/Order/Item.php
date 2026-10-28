<?php

declare(strict_types=1);

namespace App\Models\Order;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'OrderItem',
    required: ['product_id', 'quantity', 'unit_price'],
    properties: [
        new OAT\Property(property: 'id', description: 'Order item ID', type: 'integer', example: 1),
        new OAT\Property(property: 'order_id', description: 'Order ID', type: 'integer', example: 1),
        new OAT\Property(property: 'order_number', description: 'Order number', type: 'string'),
        new OAT\Property(property: 'product_id', description: 'Product ID', type: 'integer', example: 1),
        new OAT\Property(property: 'quantity', description: 'Quantity of items', type: 'integer', example: 3),
        new OAT\Property(property: 'unit_price', description: 'Unit price', type: 'number', format: 'float', example: 3.5),
        new OAT\Property(property: 'discount', description: 'Discount percentage', type: 'number', format: 'float', example: 10.0),
        new OAT\Property(property: 'tax', description: 'Tax percentage', type: 'number', format: 'float', example: 21.0),
    ],
    type: 'object'
)]
final class Item extends Model
{
    use HasFactory;

    protected $table = 'order_item';

    protected $fillable = [
        'order_id',
        'order_number',
        'product_id',
        'quantity',
        'unit_price',
        'discount',
        'tax',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $with = ['product'];

    /**
     * Get the order that owns the item.
     *
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return HasOne<Product, $this>
     */
    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function getSubTotal(): float
    {
        $amount = $this->getAttribute('unit_price') * $this->getAttribute('quantity');

        return $amount - (($this->getAttribute('discount') / 100) * $amount);
    }

    /**
     * Unit prices include taxes, so the tax is the part of the subtotal above subtotal / (1 + tax%).
     */
    public function getTaxAmount(): float
    {
        $subtotal = $this->getSubTotal();

        return $subtotal - $subtotal / (1 + ((float) $this->getAttribute('tax') / 100));
    }
}
