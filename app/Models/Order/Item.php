<?php

declare(strict_types=1);

namespace App\Models\Order;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'OrderItem', required: ['product_id', 'quantity', 'unit_price'])]
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

    #[OAT\Property(type: 'int', example: 1)]
    private ?int $order_id = null;

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $product_id = null;

    #[OAT\Property(type: 'int', example: 3)]
    protected ?int $quantity = null;

    #[OAT\Property(type: 'float', example: 3.5)]
    protected ?float $unit_price = null;

    #[OAT\Property(type: 'float', example: 10.0)]
    protected ?float $discount = null;

    #[OAT\Property(type: 'float', example: 21.0)]
    protected ?float $tax = null;

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

    public function getSubTotal(): float
    {
        $amount = $this->getAttribute('unit_price') * $this->getAttribute('quantity');

        return $amount - (($this->getAttribute('discount') / 100) * $amount);
    }

    public function getTaxAmount(): float
    {
        return ($this->getAttribute('tax') / 100) * $this->getSubTotal();
    }
}
