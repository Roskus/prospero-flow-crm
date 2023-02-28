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

#[OAT\Schema(schema: 'Order', required: ['customer_id', 'amount', 'items'])]
final class Order extends Model
{
    use SoftDeletes, HasFactory;

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
    ];

    protected $casts = [
        'created_at' => 'date',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    #[OAT\Property(type: 'int', example: 1)]
    private ?int $id;

    private ?int $company_id = null;

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $customer_id;

    #[OAT\Property(type: 'float', example: 72.30)]
    protected float $amount = 0.0;

    protected ?int $status;

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('orderCreated', function (Builder $builder): void {
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
     * Return amount - discounts + taxes
     */
    public function getTotal(): float
    {
        return $this->getAmount();
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
        return Order::with('customer')->where('company_id', $company_id)->paginate(10);
    }
}
