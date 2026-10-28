<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\CurrencyHelper;
use App\Models\Order\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OAT;
use Yajra\Auditable\AuditableWithDeletesTrait;

#[OAT\Schema(
    schema: 'Order',
    required: ['customer_id', 'amount', 'currency', 'items'],
    properties: [
        new OAT\Property(property: 'id', description: 'Order ID', type: 'integer', example: 1),
        new OAT\Property(property: 'order_number', description: 'Order number', type: 'string', example: 'ORD-2026-001'),
        new OAT\Property(property: 'customer_id', description: 'Customer ID', type: 'integer', example: 1),
        new OAT\Property(property: 'seller_id', description: 'Seller ID', type: 'integer', example: 1),
        new OAT\Property(property: 'amount', description: 'Order amount', type: 'number', format: 'float', example: 72.30),
        new OAT\Property(property: 'currency', description: 'Order currency, the company currency when omitted', type: 'string', example: 'EUR'),
        new OAT\Property(property: 'exchange_rate', description: 'Units of the company currency per unit of the order currency when the currency was set', type: 'string', example: '1.000000', readOnly: true),
        new OAT\Property(property: 'branch_id', description: 'Branch of the order; defaults to the current branch of the user', type: 'integer', example: 1),
        new OAT\Property(property: 'status', description: 'Order status', type: 'integer', example: 1),
        new OAT\Property(property: 'order_date', description: 'Order date; defaults to the creation date', type: 'string', format: 'date', example: '2026-10-27'),
        new OAT\Property(property: 'items', description: 'Order items', type: 'array', items: new OAT\Items(type: 'object')),
    ],
    type: 'object'
)]
final class Order extends Model
{
    use AuditableWithDeletesTrait;
    use HasFactory, SoftDeletes;

    const int CANCELED = 0;

    const int PENDING = 1;

    const int CONFIRMED = 2;

    const int COMPLETED = 3;

    protected $table = 'order';

    protected $fillable = [
        'company_id',
        'order_number',
        'branch_id',
        'customer_id',
        'seller_id',
        'amount',
        'currency',
        'status',
        'order_date',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'created_at' => 'date',
        'order_date' => 'date:Y-m-d',
        'exchange_rate' => 'decimal:6',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $with = ['customer', 'items'];

    protected static function boot(): void
    {
        parent::boot();

        self::addGlobalScope('orderCreated', function (Builder $builder): void {
            $builder->orderby('order_date', 'DESC')->orderby('created_at', 'DESC');
        });

        self::creating(function (Order $order): void {
            $order->order_date ??= ($order->created_at ?? now())->toDateString();
        });
    }

    // Constructor
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (! array_key_exists('company_id', $attributes)) {
            $this->company_id = 0;
        }

        if (! array_key_exists('customer_id', $attributes)) {
            $this->customer_id = 0;
        }

        if (! array_key_exists('status', $attributes)) {
            $this->setAttribute('status', Order::PENDING);
        }

        if (! array_key_exists('created_at', $attributes)) {
            $this->setAttribute('created_at', now());
        }

        if (! array_key_exists('amount', $attributes)) {
            $this->amount = 0.0;
        }
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

    /**
     * Deleted branches keep their orders, so the branch is loaded even when soft deleted.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function seller(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    /**
     * Order numbers are only unique per company, so items are linked by order id.
     *
     * @return HasMany<Item, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * The invoice of the order that is not cancelled; an order has at most one.
     *
     * @return HasOne<Invoice, $this>
     */
    public function activeInvoice(): HasOne
    {
        return $this->hasOne(Invoice::class)->where('status', '!=', Invoice::CANCELLED);
    }

    public function isInvoiced(): bool
    {
        return $this->activeInvoice()->exists();
    }

    /**
     * Error shown when an invoiced order is changed, naming its invoice; null when the order is not invoiced.
     */
    public function invoicedMessage(): ?string
    {
        $invoice = $this->activeInvoice()->first();

        return $invoice !== null
            ? __('The order is invoiced in :number. Cancel the invoice to change it.', ['number' => $invoice->fullNumber()])
            : null;
    }

    /**
     * Confirmed orders, and completed ones from before invoicing existed, can be invoiced while they have no active invoice.
     */
    public function canBeInvoiced(): bool
    {
        return in_array((int) $this->status, [self::CONFIRMED, self::COMPLETED], true)
            && ($this->relationLoaded('activeInvoice') ? $this->activeInvoice === null : ! $this->isInvoiced());
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
        $orderNumber = $this->getAttribute('order_number');

        return $orderNumber ? str_pad((string) $orderNumber, 10, '0', STR_PAD_LEFT) : '';
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
        return $this->items->sum(function ($item) {
            $tax = 0.0;
            $price = $item->getSubTotal();
            if (isset($item['tax'])) {
                $tax = $price * ($item['tax'] / 100);
            }

            return $tax;
        });
    }

    /**
     * Return amount - discounts + taxes
     */
    public function getTotal(): float
    {
        return $this->items->sum(function ($item) {
            return $item->getSubTotal();
        });
    }

    /**
     * Set the amount from the saved items.
     */
    public function refreshAmount(): void
    {
        $this->setAmount($this->load('items')->getTotal());
        $this->save();
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

    /**
     * Sales (confirmed and completed orders) of the current month compared with the previous one.
     *
     * @return array{current_month_total: float, previous_month_total: float, change_percentage: float|null, current_month_orders: int, pending_orders: int}
     */
    public function getSalesSummary(int $companyId): array
    {
        $salesBetween = fn (Carbon $start, Carbon $end) => Order::withoutGlobalScope('orderCreated')
            ->where('company_id', $companyId)
            ->whereIn('status', [self::CONFIRMED, self::COMPLETED])
            ->whereBetween('order_date', [$start->toDateString(), $end->toDateString()]);

        $now = Carbon::now();
        $previousMonth = $now->copy()->subMonthNoOverflow();

        $currentMonthSales = $salesBetween($now->copy()->startOfMonth(), $now->copy()->endOfMonth());
        $currentMonthTotal = (float) $currentMonthSales->clone()->sum(DB::raw('amount * exchange_rate'));
        $previousMonthTotal = (float) $salesBetween($previousMonth->copy()->startOfMonth(), $previousMonth->copy()->endOfMonth())->sum(DB::raw('amount * exchange_rate'));

        return [
            'current_month_total' => $currentMonthTotal,
            'previous_month_total' => $previousMonthTotal,
            'change_percentage' => $previousMonthTotal > 0
                ? round(($currentMonthTotal - $previousMonthTotal) / $previousMonthTotal * 100, 1)
                : null,
            'current_month_orders' => $currentMonthSales->count(),
            'pending_orders' => $this->getPendingCount($companyId),
        ];
    }

    /**
     * Sales KPIs between two dates, optionally for a single seller.
     * Sales are confirmed and completed orders, as in getSalesSummary().
     *
     * @return array{total: float, orders: int, average_ticket: float, pending_orders: int}
     */
    public function getSalesKpis(int $companyId, Carbon $from, Carbon $to, ?int $sellerId = null): array
    {
        $orders = Order::withoutGlobalScope('orderCreated')
            ->where('company_id', $companyId)
            ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->when($sellerId, fn (Builder $query) => $query->where('seller_id', $sellerId));

        $sales = $orders->clone()->whereIn('status', [self::CONFIRMED, self::COMPLETED]);
        $total = (float) $sales->clone()->sum(DB::raw('amount * exchange_rate'));
        $count = $sales->count();

        return [
            'total' => $total,
            'orders' => $count,
            'average_ticket' => $count > 0 ? round($total / $count, 2) : 0.0,
            'pending_orders' => $orders->clone()->where('status', self::PENDING)->count(),
        ];
    }

    /**
     * Sales KPIs of a period compared with the previous period of the same length.
     *
     * @return array{
     *     current: array{total: float, orders: int, average_ticket: float, pending_orders: int},
     *     previous: array{total: float, orders: int, average_ticket: float, pending_orders: int},
     *     change: array{total: float|null, orders: float|null, average_ticket: float|null, pending_orders: float|null},
     *     previous_from: Carbon,
     *     previous_to: Carbon
     * }
     */
    public function getSalesReport(int $companyId, Carbon $from, Carbon $to, ?int $sellerId = null): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();
        $days = (int) $from->diffInDays($to->copy()->startOfDay()) + 1;
        $previousTo = $from->copy()->subDay()->endOfDay();
        $previousFrom = $previousTo->copy()->subDays($days - 1)->startOfDay();

        $current = $this->getSalesKpis($companyId, $from, $to, $sellerId);
        $previous = $this->getSalesKpis($companyId, $previousFrom, $previousTo, $sellerId);

        $change = [];
        foreach ($current as $kpi => $value) {
            $change[$kpi] = $previous[$kpi] > 0
                ? round(($value - $previous[$kpi]) / $previous[$kpi] * 100, 1)
                : null;
        }

        return [
            'current' => $current,
            'previous' => $previous,
            'change' => $change,
            'previous_from' => $previousFrom,
            'previous_to' => $previousTo,
        ];
    }

    public function getAllActiveByCompany(int $company_id, ?int $branchId = null)
    {
        return Order::where('company_id', $company_id)
            ->when($branchId !== null, fn (Builder $query) => $query->where('branch_id', $branchId))
            ->with(['branch', 'activeInvoice'])
            ->paginate(10)
            ->withQueryString();
    }

    public function getAmountFormated(): string
    {
        return CurrencyHelper::format($this->getTotal());
    }
}
