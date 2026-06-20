<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Transaction\Category;
use App\Models\Bank\Account as BankAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'Transaction', required: ['issue_date', 'name', 'amount', 'type'], type: 'object')]
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

    #[OAT\Property(property: 'id', type: 'integer', example: 1)]
    #[OAT\Property(property: 'issue_date', type: 'string', format: 'date', example: '2025-01-15')]
    #[OAT\Property(property: 'due_date', type: 'string', format: 'date', example: '2025-02-15', nullable: true)]
    #[OAT\Property(property: 'payment_date', type: 'string', format: 'date', example: '2025-01-20', nullable: true)]
    #[OAT\Property(property: 'name', type: 'string', example: 'Invoice #123')]
    #[OAT\Property(property: 'type', type: 'string', enum: ['income', 'expense'], example: 'income')]
    #[OAT\Property(property: 'transaction_category_id', type: 'integer', example: 1, nullable: true)]
    #[OAT\Property(property: 'bank_account_id', type: 'integer', example: 1, nullable: true)]
    #[OAT\Property(property: 'bank_card_id', type: 'integer', example: 1, nullable: true)]
    #[OAT\Property(property: 'amount', type: 'number', format: 'float', example: 999.75)]
    #[OAT\Property(property: 'status', type: 'string', enum: ['pending', 'paid', 'overdue'], example: 'pending')]
    #[OAT\Property(property: 'reference', type: 'string', example: 'INV-2025-001', nullable: true)]
    #[OAT\Property(property: 'customer_id', type: 'integer', example: 1, nullable: true)]
    #[OAT\Property(property: 'supplier_id', type: 'integer', example: 1, nullable: true)]
    #[OAT\Property(property: 'notes', type: 'string', example: 'Payment for services', nullable: true)]
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
