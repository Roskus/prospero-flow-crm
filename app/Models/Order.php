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
     * Taxes included in the order total.
     */
    public function getTax(): float
    {
        return $this->items->sum(fn (Item $item): float => $item->getTaxAmount());
    }

    /**
     * Return amount - discounts, taxes included
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

    /**
     * Sales and orders of each month of a year (1 to 12), in the company currency.
     *
     * @return array<int, array{total: float, orders: int}>
     */
    public function getMonthlySales(int $companyId, int $year, ?int $sellerId = null): array
    {
        $months = Order::withoutGlobalScope('orderCreated')
            ->where('company_id', $companyId)
            ->whereIn('status', [self::CONFIRMED, self::COMPLETED])
            ->whereBetween('order_date', ["{$year}-01-01", "{$year}-12-31"])
            ->when($sellerId, fn (Builder $query) => $query->where('seller_id', $sellerId))
            ->selectRaw('MONTH(order_date) AS month, SUM(amount * exchange_rate) AS total, COUNT(*) AS orders')
            ->groupByRaw('MONTH(order_date)')
            ->toBase()
            ->get()
            ->keyBy('month');

        $sales = [];
        for ($month = 1; $month <= 12; $month++) {
            $row = $months->get($month);
            $sales[$month] = [
                'total' => round((float) ($row->total ?? 0), 2),
                'orders' => (int) ($row->orders ?? 0),
            ];
        }

        return $sales;
    }

    /**
     * Total sales between two dates in the company currency.
     */
    public function getSalesTotal(int $companyId, Carbon $from, Carbon $to, ?int $sellerId = null): float
    {
        return round((float) Order::withoutGlobalScope('orderCreated')
            ->where('company_id', $companyId)
            ->whereIn('status', [self::CONFIRMED, self::COMPLETED])
            ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->when($sellerId, fn (Builder $query) => $query->where('seller_id', $sellerId))
            ->sum(DB::raw('amount * exchange_rate')), 2);
    }

    /**
     * Products with the highest sales between two dates, with the quantity sold. Lines are joined to their
     * order by id, and deleted products keep their name.
     *
     * @return list<array{name: string, quantity: int, total: float}>
     */
    public function getTopProducts(int $companyId, Carbon $from, Carbon $to, ?int $sellerId = null, int $limit = 10): array
    {
        return DB::table('order_item')
            ->join('order', 'order.id', '=', 'order_item.order_id')
            ->leftJoin('product', 'product.id', '=', 'order_item.product_id')
            ->where('order.company_id', $companyId)
            ->whereNull('order.deleted_at')
            ->whereIn('order.status', [self::CONFIRMED, self::COMPLETED])
            ->whereBetween('order.order_date', [$from->toDateString(), $to->toDateString()])
            ->when($sellerId, fn ($query) => $query->where('order.seller_id', $sellerId))
            ->groupBy('order_item.product_id', 'product.name')
            ->selectRaw('product.name AS name, SUM(order_item.quantity) AS quantity, SUM(order_item.unit_price * order_item.quantity * (1 - COALESCE(order_item.discount, 0) / 100) * order.exchange_rate) AS total')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn (object $row): array => ['name' => (string) ($row->name ?? '—'), 'quantity' => (int) $row->quantity, 'total' => round((float) $row->total, 2)])
            ->all();
    }

    /**
     * Customers with the highest sales between two dates, with their number of orders.
     *
     * @return list<array{name: string, orders: int, total: float}>
     */
    public function getTopCustomers(int $companyId, Carbon $from, Carbon $to, ?int $sellerId = null, int $limit = 10): array
    {
        return DB::table('order')
            ->leftJoin('customer', 'customer.id', '=', 'order.customer_id')
            ->where('order.company_id', $companyId)
            ->whereNull('order.deleted_at')
            ->whereIn('order.status', [self::CONFIRMED, self::COMPLETED])
            ->whereBetween('order.order_date', [$from->toDateString(), $to->toDateString()])
            ->when($sellerId, fn ($query) => $query->where('order.seller_id', $sellerId))
            ->groupBy('order.customer_id', 'customer.name', 'customer.business_name')
            ->selectRaw('customer.name AS name, customer.business_name AS business_name, COUNT(*) AS orders, SUM(order.amount * order.exchange_rate) AS total')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn (object $row): array => ['name' => trim(($row->name ?? '—').' '.($row->business_name ?? '')), 'orders' => (int) $row->orders, 'total' => round((float) $row->total, 2)])
            ->all();
    }

    /**
     * Sales of each seller between two dates, highest first.
     *
     * @return list<array{name: string, orders: int, total: float}>
     */
    public function getSalesBySeller(int $companyId, Carbon $from, Carbon $to, ?int $sellerId = null): array
    {
        return DB::table('order')
            ->leftJoin('user', 'user.id', '=', 'order.seller_id')
            ->where('order.company_id', $companyId)
            ->whereNull('order.deleted_at')
            ->whereIn('order.status', [self::CONFIRMED, self::COMPLETED])
            ->whereBetween('order.order_date', [$from->toDateString(), $to->toDateString()])
            ->when($sellerId, fn ($query) => $query->where('order.seller_id', $sellerId))
            ->groupBy('order.seller_id', 'user.first_name', 'user.last_name')
            ->selectRaw('user.first_name AS first_name, user.last_name AS last_name, COUNT(*) AS orders, SUM(order.amount * order.exchange_rate) AS total')
            ->orderByDesc('total')
            ->get()
            ->map(fn (object $row): array => ['name' => trim(($row->first_name ?? '').' '.($row->last_name ?? '')) ?: '—', 'orders' => (int) $row->orders, 'total' => round((float) $row->total, 2)])
            ->all();
    }

    /**
     * Annual sales report: months of the year against the previous year, totals (year to date for the
     * current year) and rankings.
     *
     * @return array{year: int, months: array<int, array{total: float, orders: int, previous_total: float, previous_orders: int, change: float|null}>, total: float, previous_total: float, change: float|null, previous_until: Carbon, top_products: list<array{name: string, quantity: int, total: float}>, top_customers: list<array{name: string, orders: int, total: float}>, sellers: list<array{name: string, orders: int, total: float}>}
     */
    public function getAnnualSalesReport(int $companyId, int $year, ?int $sellerId = null): array
    {
        $current = $this->getMonthlySales($companyId, $year, $sellerId);
        $previous = $this->getMonthlySales($companyId, $year - 1, $sellerId);
        $change = fn (float $value, float $base): ?float => $base > 0 ? round(($value - $base) / $base * 100, 1) : null;

        $months = [];
        foreach ($current as $month => $sales) {
            $months[$month] = [
                ...$sales,
                'previous_total' => $previous[$month]['total'],
                'previous_orders' => $previous[$month]['orders'],
                'change' => $change($sales['total'], $previous[$month]['total']),
            ];
        }

        $from = Carbon::create($year, 1, 1)->startOfDay();
        $to = Carbon::create($year, 12, 31)->startOfDay();
        $isCurrentYear = $year === Carbon::now()->year;
        $previousUntil = $isCurrentYear ? Carbon::now()->subYearNoOverflow()->startOfDay() : Carbon::createStrict($year - 1, 12, 31)->startOfDay();
        $total = $this->getSalesTotal($companyId, $from, $to, $sellerId);
        $previousTotal = $this->getSalesTotal($companyId, Carbon::create($year - 1, 1, 1), $previousUntil, $sellerId);

        return [
            'year' => $year,
            'months' => $months,
            'total' => $total,
            'previous_total' => $previousTotal,
            'change' => $change($total, $previousTotal),
            'previous_until' => $previousUntil,
            'top_products' => $this->getTopProducts($companyId, $from, $to, $sellerId),
            'top_customers' => $this->getTopCustomers($companyId, $from, $to, $sellerId),
            'sellers' => $this->getSalesBySeller($companyId, $from, $to, $sellerId),
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
