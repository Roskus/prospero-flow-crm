<?php

declare(strict_types=1);

namespace App\Models\Bank;

use App\Models\Bank;
use App\Models\BankCard;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;

#[OAT\Schema(
    schema: 'BankAccount',
    required: ['type', 'country_id', 'currency'],
    properties: [
        new OAT\Property(property: 'id', description: 'Bank account ID', type: 'integer', example: 1),
        new OAT\Property(property: 'type', description: 'Account type', type: 'string', enum: ['bank', 'stripe', 'paypal', 'mercadopago', 'other'], example: 'bank'),
        new OAT\Property(property: 'account_name', description: 'Account name', type: 'string', example: 'Main account'),
        new OAT\Property(property: 'bank_id', description: 'Bank ID', type: 'integer', example: 1),
        new OAT\Property(property: 'country_id', description: 'Country ISO code', type: 'string', example: 'ES'),
        new OAT\Property(property: 'currency', description: 'Account currency', type: 'string', example: 'EUR'),
        new OAT\Property(property: 'iban', description: 'IBAN number', type: 'string', example: 'ES9121000418450200051332'),
        new OAT\Property(property: 'account_number', description: 'Account number', type: 'string', example: '12345678'),
        new OAT\Property(property: 'swift', description: 'SWIFT code', type: 'string', example: 'BSCHESMMXXX'),
        new OAT\Property(property: 'sort_code', description: 'Sort code', type: 'string', example: '20-00-00'),
        new OAT\Property(property: 'cbu', description: 'CBU number', type: 'string', example: '0110048200000004823002'),
        new OAT\Property(property: 'alias', description: 'Account alias', type: 'string', example: 'MI.CUENTA.AR'),
        new OAT\Property(property: 'bizum', description: 'Bizum enabled', type: 'boolean'),
        new OAT\Property(property: 'opening_balance', description: 'Opening balance', type: 'number', format: 'float', example: 0.00),
        new OAT\Property(property: 'notes', description: 'Account notes', type: 'string', example: 'Main operating account'),
    ],
    type: 'object'
)]
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
        return $this->hasMany(Transaction::class, 'bank_account_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(BankCard::class, 'bank_account_id');
    }

    public function balance(): float
    {
        $paid = $this->transactions()->where('status', Transaction::PAID);

        $income = (float) $paid->where('type', Transaction::INCOME)->sum('amount');
        $expense = (float) $paid->where('type', Transaction::EXPENSE)->sum('amount');

        return (float) ($this->opening_balance ?? 0) + $income - $expense;
    }

    public function getAllByCompanyId(int $companyId)
    {
        return Account::where('company_id', $companyId)
            ->with(['bank', 'country'])
            ->get();
    }
}
