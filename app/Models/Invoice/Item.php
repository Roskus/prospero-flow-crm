<?php

declare(strict_types=1);

namespace App\Models\Invoice;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'InvoiceItem',
    properties: [
        new OAT\Property(property: 'id', description: 'Invoice item ID', type: 'integer', example: 1),
        new OAT\Property(property: 'invoice_id', description: 'Invoice ID', type: 'integer', example: 1),
        new OAT\Property(property: 'product_id', description: 'Product ID at the moment of issue', type: 'integer', example: 1),
        new OAT\Property(property: 'description', description: 'Product name at the moment of issue', type: 'string', example: 'Coffee'),
        new OAT\Property(property: 'sku', description: 'Product SKU at the moment of issue', type: 'string', example: 'CF-01'),
        new OAT\Property(property: 'quantity', description: 'Quantity', type: 'number', format: 'float', example: 3),
        new OAT\Property(property: 'unit_price', description: 'Unit price', type: 'number', format: 'float', example: 10.0),
        new OAT\Property(property: 'discount', description: 'Discount percentage', type: 'number', format: 'float', example: 10.0),
        new OAT\Property(property: 'tax', description: 'Tax percentage', type: 'number', format: 'float', example: 21.0),
        new OAT\Property(property: 'subtotal', description: 'Quantity × unit price minus the discount, rounded', type: 'number', format: 'float', example: 27.0),
        new OAT\Property(property: 'tax_amount', description: 'Subtotal × tax percentage, rounded', type: 'number', format: 'float', example: 5.67),
        new OAT\Property(property: 'total', description: 'Subtotal plus tax', type: 'number', format: 'float', example: 32.67),
    ],
    type: 'object'
)]
final class Item extends Model
{
    use HasFactory;

    protected $table = 'invoice_item';

    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'sku',
        'quantity',
        'unit_price',
        'discount',
        'tax',
        'subtotal',
        'tax_amount',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'unit_price' => 'decimal:5',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * The product may have been deleted since the invoice was issued; the line keeps its own description.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
