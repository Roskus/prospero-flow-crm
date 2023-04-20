<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 *  @OA\Schema(
 *    schema="Product",
 *    type="object",
 *    required={"category_id", "name"},
 *    @OA\Property(
 *        property="category_id",
 *        description="Category ID of the product",
 *        type="int",
 *        example="1"
 *    ),
 *    @OA\Property(
 *        property="brand_id",
 *        description="Brand ID of the product",
 *        type="int",
 *        example="2"
 *    ),
 *    @OA\Property(
 *        property="name",
 *        description="Name of the product",
 *        type="string",
 *        example="Hervidor Russell Hobbs"
 *    ),
 *    @OA\Property(
 *        property="model",
 *        description="Model of the product",
 *        type="string",
 *        example="Colours Plus+"
 *    ),
 *    @OA\Property(
 *        property="sku",
 *        description="SKU of the product",
 *        type="string",
 *        example="RUS*24994-70"
 *    ),
 *    @OA\Property(
 *        property="barcode",
 *        description="EAN-13 barcode of the product",
 *        type="string",
 *        example="4008496982943"
 *    ),
 *    @OA\Property(
 *        property="photo",
 *        description="Photo of the product",
 *        type="string",
 *        example="product.jpg"
 *    ),
 *    @OA\Property(
 *        property="cost",
 *        description="Cost of the product",
 *        type="number",
 *        example="10.5"
 *    ),
 *    @OA\Property(
 *        property="price",
 *        description="Price of the product",
 *        type="number",
 *        example="21.70"
 *    ),
 *    @OA\Property(
 *        property="tax",
 *        description="Tax of the product",
 *        type="number",
 *        example="21.00"
 *    ),
 *    @OA\Property(
 *        property="currency",
 *        description="Currency of the product",
 *        type="string",
 *        example="USD"
 *    ),
 *    @OA\Property(
 *        property="min_stock_quantity",
 *        description="Min Stock Quantity of the product",
 *        type="int",
 *        example="10"
 *    ),
 *    @OA\Property(
 *        property="quantity",
 *        description="Quantity of the product",
 *        type="int",
 *        example="33"
 *    ),
 *    @OA\Property(
 *        property="description",
 *        description="Description of the product",
 *        type="string",
 *        example="This is a product description"
 *    ),
 *    @OA\Property(
 *        property="elaboration_date",
 *        description="Elaboration date of the product",
 *        type="string",
 *        format="date",
 *        example="2023-01-01"
 *    ),
 *    @OA\Property(
 *        property="expiration_date",
 *        description="Expiration date of the product",
 *        type="string",
 *        format="date",
 *        example="2023-12-01"
 *    ),
 *    @OA\Property(
 *        property="tags",
 *        description="Tags of the product",
 *        type="string",
 *        example="['hervidor', 'pava electrica']"
 *    )
 *  )
 */
class Product extends Model
{
    use SoftDeletes, HasFactory;

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
        'capacity',
        'capacity_measure',
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

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(\App\Models\Category::class, 'id', 'category_id');
    }

    public function brand(): HasOne
    {
        return $this->hasOne(\App\Models\Brand::class, 'id', 'brand_id');
    }

    public function getAll()
    {
        return Product::orderBy('name', 'asc')->get();
    }

    public function getAllByCompanyId(int $company_id, ?string $search = null, ?array $filters = null)
    {
        $products = Product::with('category', 'brand')->where('company_id', '=', $company_id);
        if (! empty($search)) {
            $products->where('name', 'LIKE', "%$search%")
                ->orWhere('sku', 'LIKE', "%$search%");
        }

        return $products->orderBy('name', 'asc')->paginate(10);
    }
}
