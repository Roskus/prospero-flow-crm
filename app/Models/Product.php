<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    const ACTIVE = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
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
        'description',
    ];

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }

    public function brand()
    {
        return $this->hasOne('App\Models\Brand', 'id', 'brand_id');
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
