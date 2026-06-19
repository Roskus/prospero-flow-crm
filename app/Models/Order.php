<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\CurrencyHelper;
use App\Models\Order\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
    use AuditableWithDeletesTrait;
    use HasFactory, SoftDeletes;

    // Class Constants
    const int CANCELED = 0;

    const int PENDING = 1;

    const int CONFIRMED = 2;

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
    private ?int $id; // NOSONAR

    private ?int $company_id = null; // NOSONAR

    #[OAT\Property(type: 'string', example: 'ORD-2026-001')]
    protected ?string $order_number;

    #[OAT\Property(type: 'int', example: 1)]
    protected ?int $customer_id;

    #[OAT\Property(type: 'float', example: 72.30)]
    protected float $amount = 0.0;

    #[OAT\Property(type: 'string', example: 'EUR')]
    protected string $currency = 'EUR';

    protected ?int $status;

    #[OAT\Property(type: 'array')]
    protected ?array $items = [];

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

    // #[OAT\Property()]
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'order_number', 'order_number');
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

    public function orderNumber(): string
    {
        return isset($this->order_number) ? str_pad((string) $this->order_number, 10, '0', STR_PAD_LEFT) : '';
    }

    public function getStatusLabel(): string
    {
        return match ((int) $this->getAttribute('status')) {
            self::CANCELED => 'Canceled',
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::COMPLETED => 'Completed',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ((int) $this->getAttribute('status')) {
            self::CANCELED => 'danger',
            self::PENDING => 'warning',
            self::CONFIRMED => 'primary',
            self::COMPLETED => 'success',
            default => 'secondary',
        };
    }

    /**
     * Return taxes
     */
    public function getTax(): float
    {
        $items = $this->items;
        $total = $items->sum(function ($item) {
            $tax = 0.0;
            $price = $item->getSubTotal();
            if (isset($item['tax'])) {
                $tax = $price * ($item['tax'] / 100);
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
            return $item->getSubTotal();
        });

        return $total;
    }

    public function getAll(): Collection
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

    public function getAmountFormated(): string
    {
        return CurrencyHelper::format($this->getTotal());
    }
}
