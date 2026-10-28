<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Bank\Account as BankAccount;
use App\Models\Transaction\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Transaction',
    required: ['issue_date', 'name', 'amount', 'type'],
    properties: [
        new OAT\Property(property: 'id', description: 'Transaction ID', type: 'integer', example: 1),
        new OAT\Property(property: 'issue_date', description: 'Issue date', type: 'string', format: 'date', example: '2025-01-15'),
        new OAT\Property(property: 'due_date', description: 'Due date', type: 'string', format: 'date', example: '2025-02-15'),
        new OAT\Property(property: 'payment_date', description: 'Payment date', type: 'string', format: 'date', example: '2025-01-20'),
        new OAT\Property(property: 'name', description: 'Transaction name', type: 'string', example: 'Invoice #123'),
        new OAT\Property(property: 'type', description: 'Transaction type', type: 'string', enum: ['income', 'expense'], example: 'income'),
        new OAT\Property(property: 'transaction_category_id', description: 'Transaction category ID', type: 'integer', example: 1),
        new OAT\Property(property: 'bank_account_id', description: 'Bank account ID', type: 'integer', example: 1),
        new OAT\Property(property: 'bank_card_id', description: 'Bank card ID', type: 'integer', example: 1),
        new OAT\Property(property: 'amount', description: 'Transaction amount', type: 'number', format: 'float', example: 999.75),
        new OAT\Property(property: 'currency', description: 'Currency of the amount; defaults to the account currency, or the company currency without account', type: 'string', example: 'EUR'),
        new OAT\Property(property: 'exchange_rate', description: 'Units of the company currency per unit of the transaction currency', type: 'string', example: '1.000000'),
        new OAT\Property(property: 'payment_method', description: 'Payment method', type: 'string', enum: ['cash', 'card', 'transfer', 'mercadopago', 'bizum', 'other'], example: 'transfer'),
        new OAT\Property(property: 'invoice_id', description: 'Invoice paid by this transaction', type: 'integer', example: 1, readOnly: true),
        new OAT\Property(property: 'invoice_amount', description: 'Amount settled in the invoice currency', type: 'number', format: 'float', example: 50.0, readOnly: true),
        new OAT\Property(property: 'status', description: 'Transaction status', type: 'string', enum: ['pending', 'paid', 'overdue'], example: 'pending'),
        new OAT\Property(property: 'reference', description: 'Reference number', type: 'string', example: 'INV-2025-001'),
        new OAT\Property(property: 'customer_id', description: 'Customer ID', type: 'integer', example: 1),
        new OAT\Property(property: 'supplier_id', description: 'Supplier ID', type: 'integer', example: 1),
        new OAT\Property(property: 'notes', description: 'Notes', type: 'string', example: 'Payment for services'),
        new OAT\Property(property: 'attachment', description: 'Attachment filename', type: 'string'),
    ],
    type: 'object'
)]
class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    const string INCOME = 'income';

    const string EXPENSE = 'expense';

    const string PENDING = 'pending';

    const string PAID = 'paid';

    const string OVERDUE = 'overdue';

    const string METHOD_CASH = 'cash';

    const string METHOD_CARD = 'card';

    const string METHOD_TRANSFER = 'transfer';

    const string METHOD_MERCADOPAGO = 'mercadopago';

    const string METHOD_BIZUM = 'bizum';

    const string METHOD_OTHER = 'other';

    /**
     * Payment methods and their labels.
     */
    const array PAYMENT_METHODS = [
        self::METHOD_CASH => 'In cash',
        self::METHOD_CARD => 'Card',
        self::METHOD_TRANSFER => 'Transfer',
        self::METHOD_MERCADOPAGO => 'MercadoPago',
        self::METHOD_BIZUM => 'Bizum',
        self::METHOD_OTHER => 'Other',
    ];

    /**
     * Methods that need the destination account of the company.
     */
    const array METHODS_REQUIRING_ACCOUNT = [self::METHOD_TRANSFER, self::METHOD_BIZUM];

    protected $table = 'transaction';

    protected $fillable = [
        'company_id',
        'issue_date',
        'due_date',
        'payment_date',
        'name',
        'type',
        'transaction_category_id',
        'bank_account_id',
        'bank_card_id',
        'amount',
        'currency',
        'exchange_rate',
        'payment_method',
        'status',
        'reference',
        'customer_id',
        'supplier_id',
        'notes',
        'attachment',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'invoice_amount' => 'decimal:5',
        'issue_date' => 'date:Y-m-d',
        'due_date' => 'date:Y-m-d',
        'payment_date' => 'date:Y-m-d',
    ];

    protected $hidden = [
        'company_id',
        'deleted_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'transaction_category_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function bankCard(): BelongsTo
    {
        return $this->belongsTo(BankCard::class, 'bank_card_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return BelongsTo<Invoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Transactions of a company matching the given filters. The text search is grouped so it can never
     * bypass the company condition.
     *
     * @param  array{search?: string|null, type?: string|null, status?: string|null, transaction_category_id?: int|string|null, from?: string|null, to?: string|null}  $filters
     */
    public function searchByCompany(int $company_id, array $filters = []): Builder
    {
        $search = $filters['search'] ?? null;

        return Transaction::where('company_id', $company_id)
            ->when(! empty($search), fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('reference', 'LIKE', "%$search%");
            }))
            ->when(! empty($filters['type']), fn (Builder $query) => $query->where('type', $filters['type']))
            ->when(! empty($filters['status']), fn (Builder $query) => $query->where('status', $filters['status']))
            ->when(! empty($filters['transaction_category_id']), fn (Builder $query) => $query->where('transaction_category_id', (int) $filters['transaction_category_id']))
            ->when(! empty($filters['from']), fn (Builder $query) => $query->whereDate('issue_date', '>=', $filters['from']))
            ->when(! empty($filters['to']), fn (Builder $query) => $query->whereDate('issue_date', '<=', $filters['to']));
    }

    public function getAllByCompany(int $company_id)
    {
        return Transaction::where('company_id', $company_id)
            ->with(['category', 'customer', 'supplier', 'bankAccount'])
            ->orderBy('issue_date', 'desc')
            ->get();
    }
}
