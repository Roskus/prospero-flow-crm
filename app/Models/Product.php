<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Product', required: ['category_id', 'name'], type: 'object')]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    const ACTIVE = 1;

    protected $table = 'product';

    protected $fillable = [
        'company_id',
        'category_id',
        'brand_id',
        'name',
        'model',
        'sku',
        'barcode',
        'photo',
        'cost',
        'price',
        'tax',
        'currency',
        'min_stock_quantity',
        'quantity',
        'weight',
        'weight_unit',
        'volume',
        'volume_unit',
        'dimensions',
        'dimension_unit',
        'description',
        'elaboration_date',
        'expiration_date',
        'tags',
        'updated_at',
        'status',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    protected $casts = [
        'elaboration_date' => 'date',
        'expiration_date' => 'date',
    ];

    #[OAT\Property(property: 'category_id', type: 'integer', example: 1)]
    #[OAT\Property(property: 'brand_id', type: 'integer', example: 2)]
    #[OAT\Property(property: 'name', type: 'string', example: 'Hervidor Russell Hobbs')]
    #[OAT\Property(property: 'model', type: 'string', example: 'Colours Plus+')]
    #[OAT\Property(property: 'sku', type: 'string', example: 'RUS*24994-70')]
    #[OAT\Property(property: 'barcode', type: 'string', example: '4008496982943')]
    #[OAT\Property(property: 'photo', type: 'string', example: 'product.jpg')]
    #[OAT\Property(property: 'cost', type: 'number', format: 'float', example: 10.5)]
    #[OAT\Property(property: 'price', type: 'number', format: 'float', example: 21.70)]
    #[OAT\Property(property: 'tax', type: 'number', format: 'float', example: 21.00)]
    #[OAT\Property(property: 'currency', type: 'string', example: 'USD')]
    #[OAT\Property(property: 'min_stock_quantity', type: 'integer', example: 10)]
    #[OAT\Property(property: 'quantity', type: 'integer', example: 33)]
    #[OAT\Property(property: 'weight', type: 'number', nullable: true, format: 'float', example: 1.25)]
    #[OAT\Property(property: 'weight_unit', type: 'string', nullable: true, enum: ['mg', 'g', 'kg', 'lb', 'oz'], example: 'kg')]
    #[OAT\Property(property: 'volume', type: 'number', nullable: true, format: 'float', example: 750)]
    #[OAT\Property(property: 'volume_unit', type: 'string', nullable: true, enum: ['ml', 'cl', 'l', 'fl oz'], example: 'ml')]
    #[OAT\Property(property: 'dimensions', type: 'string', nullable: true, example: '200 x 150 x 100')]
    #[OAT\Property(property: 'dimension_unit', type: 'string', nullable: true, enum: ['mm', 'cm', 'm', 'in'], example: 'mm')]
    #[OAT\Property(property: 'description', type: 'string', example: 'This is a product description')]
    #[OAT\Property(property: 'elaboration_date', type: 'string', format: 'date', example: '2023-01-01')]
    #[OAT\Property(property: 'expiration_date', type: 'string', format: 'date', example: '2023-12-01')]
    #[OAT\Property(property: 'tags', type: 'string', example: 'hervidor, pava electrica')]
    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    /**
     * SKU and barcode (EAN) that are filled in; both are strings, so "0" is kept.
     *
     * @return list<string>
     */
    public function identifierCodes(): array
    {
        return array_values(array_filter(
            [(string) $this->sku, (string) $this->barcode],
            fn (string $code): bool => $code !== '',
        ));
    }

    public function getAll()
    {
        return Product::orderBy('name', 'asc')->get();
    }

    /**
     * @return Builder<Product>
     */
    public function searchByCompany(int $company_id, ?string $search = null): Builder
    {
        return Product::where('company_id', $company_id)
            ->when(! empty($search), fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('sku', 'LIKE', "%$search%")
                    ->orWhere('barcode', 'LIKE', "%$search%");
            }));
    }

    public function getAllByCompanyId(int $company_id, ?string $search = null, ?array $filters = null)
    {
        return $this->searchByCompany($company_id, $search)
            ->with('category', 'brand')
            ->orderByDesc('updated_at')
            ->orderBy('name', 'asc')
            ->paginate(10);
    }
}
