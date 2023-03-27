<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Order\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;
use Yajra\Auditable\AuditableWithDeletesTrait;

#[OAT\Schema(schema: 'Order', required: ['customer_id', 'amount', 'currency', 'items'])]
final class Order extends Model
{
    use SoftDeletes, HasFactory;
    use AuditableWithDeletesTrait;

    // Class Constants
    const CANCELED = 0;

    const PENDING = 1;

    const CONFIRMED = 2;

    const COMPLETED = 3;

    // Class Properties

    protected $table = 'order';

    protected $fillable = [
        'customer_id',
        'seller_id',
        'amount',
        'currency',
        'status',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'created_at' => 'date',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $with = ['customer', 'items'];

    #[OAT\Property(type: 'int', example: 1)]
    private ?int $id;

    private ?int $company_id = null;

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $customer_id;

    #[OAT\Property(type: 'float', example: 72.30)]
    protected float $amount = 0.0;

    #[OAT\Property(type: 'string', example: 'EUR')]
    protected string $currency = 'EUR';

    protected ?int $status;

    protected static function boot(): void
    {
        parent::boot();

        self::addGlobalScope('orderCreated', function (Builder $builder): void {
            $builder->orderby('created_at', 'DESC');
        });
    }

    // Constructor
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->company_id = 0;
        $this->customer_id = 0;
        $this->setAttribute('status', Order::PENDING);
        $this->setAttribute('created_at', now());
        $this->amount = 0.0;
    }

    // Relationships
    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function seller(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    //#[OAT\Property()]
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'order_id', 'id');
    }

    // Accessors and Mutators
    public function getId(): int
    {
        return (int) $this->getAttribute('id');
    }

    public function getCompanyId(): int
    {
        return (int) $this->getAttribute('company_id');
    }

    public function setCompanyId(int $company_id): void
    {
        $this->setAttribute('company_id', $company_id);
    }

    public function getCustomerId(): int
    {
        return (int) $this->getAttribute('customer_id');
    }

    public function setCustomerId(int $customer_id): void
    {
        $this->setAttribute('customer_id', $customer_id);
    }

    public function getAmount(): float
    {
        return (float) $this->getAttribute('amount');
    }

    public function setAmount(float $amount): void
    {
        $this->setAttribute('amount', $amount);
    }

    /**
     * Return taxes
     */
    public function getTax(): float
    {
        $items = $this->items;
        $total = $items->sum(function ($item) {
            $tax = 0.0;
            $price = ($item['quantity'] * $item['unit_price']);
            if (isset($item['tax'])) {
                $tax = ($item['tax'] * 100) / $price;
            }

            return $tax;
        });

        return $total;
    }

    /**
     * Return amount - discounts + taxes
     */
    public function getTotal(): float
    {
        $items = $this->items;
        $total = $items->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });

        return $total;
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Order::all();
    }

    public function getPendingCount(int $company_id): int
    {
        return Order::where('company_id', $company_id)
                    ->where('status', self::PENDING)->count();
    }

    public function getAllActiveByCompany(int $company_id)
    {
        return Order::where('company_id', $company_id)->paginate(10);
    }
}
