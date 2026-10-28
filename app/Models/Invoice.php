<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Invoice\Item;
use App\ValueObjects\Address;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Invoice',
    properties: [
        new OAT\Property(property: 'id', description: 'Invoice ID', type: 'integer', example: 1),
        new OAT\Property(property: 'branch_id', description: 'Branch that issued the invoice', type: 'integer', example: 1),
        new OAT\Property(property: 'order_id', description: 'Invoiced order', type: 'integer', example: 1),
        new OAT\Property(property: 'customer_id', description: 'Customer ID', type: 'integer', example: 1),
        new OAT\Property(property: 'series', description: 'Series, the branch code at the moment of issue', type: 'string', example: '0001'),
        new OAT\Property(property: 'number', description: 'Number in the branch sequence', type: 'integer', example: 1),
        new OAT\Property(property: 'issue_date', description: 'Issue date', type: 'string', format: 'date', example: '2026-10-28'),
        new OAT\Property(property: 'due_date', description: 'Due date', type: 'string', format: 'date', example: '2026-11-28'),
        new OAT\Property(property: 'currency', description: 'Invoice currency', type: 'string', example: 'EUR'),
        new OAT\Property(property: 'exchange_rate', description: 'Units of the company currency per unit of the invoice currency', type: 'string', example: '1.000000'),
        new OAT\Property(property: 'subtotal', description: 'Sum of the line subtotals', type: 'number', format: 'float', example: 27.00),
        new OAT\Property(property: 'tax', description: 'Sum of the line taxes', type: 'number', format: 'float', example: 5.67),
        new OAT\Property(property: 'total', description: 'Total including taxes', type: 'number', format: 'float', example: 32.67),
        new OAT\Property(property: 'paid_amount', description: 'Amount paid in the invoice currency', type: 'number', format: 'float', example: 0),
        new OAT\Property(property: 'status', description: 'Invoice status', type: 'string', enum: ['issued', 'partially_paid', 'paid', 'cancelled'], example: 'issued'),
        new OAT\Property(property: 'notes', description: 'Notes', type: 'string'),
    ],
    type: 'object'
)]
/**
 * @property int $id
 * @property int $company_id
 * @property int $branch_id
 * @property ?int $order_id
 * @property string $series
 * @property int $number
 * @property Carbon $issue_date
 * @property Carbon $due_date
 * @property ?Carbon $cancelled_at
 * @property string $currency
 * @property string $status
 * @property array<string, mixed> $customer_data
 * @property array{company: array<string, mixed>, branch: array<string, mixed>} $issuer_data
 */
class Invoice extends Model
{
    use HasFactory;

    const string ISSUED = 'issued';

    const string PARTIALLY_PAID = 'partially_paid';

    const string PAID = 'paid';

    const string CANCELLED = 'cancelled';

    const array STATUSES = [self::ISSUED, self::PARTIALLY_PAID, self::PAID, self::CANCELLED];

    protected $table = 'invoice';

    protected $fillable = [
        'company_id',
        'branch_id',
        'order_id',
        'customer_id',
        'series',
        'number',
        'issue_date',
        'due_date',
        'currency',
        'exchange_rate',
        'subtotal',
        'tax',
        'total',
        'paid_amount',
        'status',
        'cancelled_at',
        'customer_data',
        'issuer_data',
        'notes',
        'created_by',
    ];

    protected $hidden = [
        'company_id',
    ];

    protected $casts = [
        'issue_date' => 'date:Y-m-d',
        'due_date' => 'date:Y-m-d',
        'cancelled_at' => 'datetime',
        'exchange_rate' => 'decimal:6',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'customer_data' => 'array',
        'issuer_data' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Deleted branches keep their invoices, so the branch is loaded even when soft deleted.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The printed customer data is the snapshot; the relation is for filters and links.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    #[Scope]
    protected function ofCompany(Builder $query, int $companyId): void
    {
        $query->where('company_id', $companyId);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status', '!=', self::CANCELLED);
    }

    /**
     * Series and number padded to 8 digits, as printed: `0001-00000001`.
     */
    public function fullNumber(): string
    {
        return $this->series.'-'.str_pad((string) $this->number, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Amount still owed in the invoice currency.
     */
    public function balance(): string
    {
        return bcsub((string) $this->total, (string) $this->paid_amount, 2);
    }

    public function isCancelled(): bool
    {
        return $this->status === self::CANCELLED;
    }

    /**
     * Payments are the income transactions that reference the invoice.
     *
     * @return HasMany<Transaction, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function hasPayments(): bool
    {
        return $this->payments()->exists();
    }

    public function isPaid(): bool
    {
        return $this->status === self::PAID;
    }

    /**
     * Invoices of a company matching the list filters. Overdue is not stored, so it filters by due date.
     *
     * @param  array{branch_id?: int|null, status?: string|null, customer_id?: int|null}  $filters
     */
    public static function searchByCompany(int $companyId, array $filters = []): Builder
    {
        $status = $filters['status'] ?? null;

        return self::query()
            ->ofCompany($companyId)
            ->when(! empty($filters['branch_id']), fn (Builder $query) => $query->where('branch_id', (int) $filters['branch_id']))
            ->when(! empty($filters['customer_id']), fn (Builder $query) => $query->where('customer_id', (int) $filters['customer_id']))
            ->when($status === 'overdue', fn (Builder $query) => $query
                ->whereIn('status', [self::ISSUED, self::PARTIALLY_PAID])
                ->whereDate('due_date', '<', today()))
            ->when(in_array($status, self::STATUSES, true), fn (Builder $query) => $query->where('status', $status))
            ->orderByDesc('issue_date')
            ->orderByDesc('id');
    }

    /**
     * Overdue depends on today's date, so it is derived instead of stored.
     */
    public function isOverdue(): bool
    {
        return in_array($this->status, [self::ISSUED, self::PARTIALLY_PAID], true)
            && $this->due_date->lt(today());
    }

    public function isInCompanyCurrency(): bool
    {
        return $this->currency === ($this->issuer_data['company']['currency'] ?? $this->currency);
    }

    /**
     * Total converted to the company currency with the invoice rate.
     */
    public function totalInCompanyCurrency(): string
    {
        return bcmul((string) $this->total, (string) $this->exchange_rate, 2);
    }

    /**
     * "Equivalent: ARS 120,000.00 (1 USD = 1,200.00 ARS, 22/10/2026)", or null in the company currency.
     */
    public function equivalentText(): ?string
    {
        if ($this->isInCompanyCurrency()) {
            return null;
        }

        $companyCurrency = (string) $this->issuer_data['company']['currency'];

        return __('Equivalent: :amount (1 :currency = :rate :company_currency, :date)', [
            'amount' => $companyCurrency.' '.number_format((float) $this->totalInCompanyCurrency(), 2),
            'currency' => $this->currency,
            'rate' => number_format((float) $this->exchange_rate, 2),
            'company_currency' => $companyCurrency,
            'date' => $this->issue_date->format('d/m/Y'),
        ]);
    }

    public function customerAddress(): Address
    {
        return Address::fromArray($this->customer_data['address'] ?? []);
    }

    public function branchAddress(): Address
    {
        return Address::fromArray($this->issuer_data['branch']['address'] ?? []);
    }

    /**
     * Payment badge of the invoice page and PDF: red while payment is pending, green once paid.
     *
     * @return array{label: string, color: string}
     */
    public function paymentBadge(): array
    {
        return match ($this->status) {
            self::PAID => ['label' => 'Paid in full', 'color' => '#198754'],
            self::CANCELLED => ['label' => 'Cancelled', 'color' => '#6c757d'],
            default => ['label' => 'Pending payment', 'color' => '#dc3545'],
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::ISSUED => 'Issued',
            self::PARTIALLY_PAID => 'Partially paid',
            self::PAID => 'Paid',
            self::CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::ISSUED => 'primary',
            self::PARTIALLY_PAID => 'warning',
            self::PAID => 'success',
            self::CANCELLED => 'secondary',
            default => 'secondary',
        };
    }
}
