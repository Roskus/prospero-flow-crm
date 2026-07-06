<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Bank\Account as BankAccount;
use App\Models\Transaction\Category;
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
        'status',
        'reference',
        'customer_id',
        'supplier_id',
        'notes',
        'attachment',
    ];

    protected $casts = [
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

    public function getAllByCompany(int $company_id)
    {
        return Transaction::where('company_id', $company_id)
            ->with(['category', 'customer', 'supplier', 'bankAccount'])
            ->orderBy('issue_date', 'desc')
            ->get();
    }
}
