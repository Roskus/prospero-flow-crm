<?php

declare(strict_types=1);

namespace App\Models\Bank;

use App\Models\Bank;
use App\Models\BankCard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;

#[OAT\Schema(schema: 'BankAccount', required: ['type', 'country_id', 'currency'], type: 'object')]
class Account extends Model
{
    use HasFactory;
    use SoftDeletes;

    const string TYPE_BANK = 'bank';

    const string TYPE_STRIPE = 'stripe';

    const string TYPE_PAYPAL = 'paypal';

    const string TYPE_MERCADOPAGO = 'mercadopago';

    const string TYPE_OTHER = 'other';

    const array TYPES = [
        self::TYPE_BANK => 'Bank',
        self::TYPE_STRIPE => 'Stripe',
        self::TYPE_PAYPAL => 'PayPal',
        self::TYPE_MERCADOPAGO => 'MercadoPago',
        self::TYPE_OTHER => 'Other',
    ];

    protected $table = 'bank_account';

    protected $fillable = [
        'company_id',
        'type',
        'account_name',
        'country_id',
        'bank_id',
        'currency',
        'iban',
        'account_number',
        'swift',
        'sort_code',
        'cbu',
        'alias',
        'bizum',
        'opening_balance',
        'notes',
    ];

    #[OAT\Property(property: 'type', type: 'string', enum: ['bank', 'stripe', 'paypal', 'mercadopago', 'other'], example: 'bank')]
    #[OAT\Property(property: 'account_name', type: 'string', example: 'Main account', nullable: true)]
    #[OAT\Property(property: 'bank_id', type: 'integer', example: 1, nullable: true)]
    #[OAT\Property(property: 'country_id', type: 'string', example: 'ES')]
    #[OAT\Property(property: 'currency', type: 'string', example: 'EUR')]
    #[OAT\Property(property: 'iban', type: 'string', example: 'ES9121000418450200051332', nullable: true)]
    #[OAT\Property(property: 'account_number', type: 'string', example: '12345678', nullable: true)]
    #[OAT\Property(property: 'swift', type: 'string', example: 'BSCHESMMXXX', nullable: true)]
    #[OAT\Property(property: 'sort_code', type: 'string', example: '20-00-00', nullable: true)]
    #[OAT\Property(property: 'cbu', type: 'string', example: '0110048200000004823002', nullable: true)]
    #[OAT\Property(property: 'alias', type: 'string', example: 'MI.CUENTA.AR', nullable: true)]
    #[OAT\Property(property: 'opening_balance', type: 'number', format: 'float', example: 0.00)]
    #[OAT\Property(property: 'notes', type: 'string', example: 'Main operating account', nullable: true)]
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(\App\Models\Transaction::class, 'bank_account_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(BankCard::class, 'bank_account_id');
    }

    public function balance(): float
    {
        $paid = $this->transactions()->where('status', \App\Models\Transaction::PAID);

        $income = (float) $paid->where('type', \App\Models\Transaction::INCOME)->sum('amount');
        $expense = (float) $paid->where('type', \App\Models\Transaction::EXPENSE)->sum('amount');

        return (float) ($this->opening_balance ?? 0) + $income - $expense;
    }

    public function getAllByCompanyId(int $companyId)
    {
        return Account::where('company_id', $companyId)
            ->with(['bank', 'country'])
            ->get();
    }
}
