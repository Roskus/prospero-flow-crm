<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Bank\Account as BankAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'BankCard',
    required: ['bank_account_id', 'type', 'network', 'cardholder_name', 'expires_month', 'expires_year'],
    properties: [
        new OAT\Property(property: 'id', description: 'Bank card ID', type: 'integer', example: 1),
        new OAT\Property(property: 'bank_account_id', description: 'Bank account ID', type: 'integer', example: 1),
        new OAT\Property(property: 'type', description: 'Card type', type: 'string', enum: ['debit', 'credit'], example: 'debit'),
        new OAT\Property(property: 'network', description: 'Card network', type: 'string', enum: ['visa', 'mastercard', 'amex', 'other'], example: 'visa'),
        new OAT\Property(property: 'cardholder_name', description: 'Cardholder name', type: 'string', maxLength: 80, example: 'John Doe'),
        new OAT\Property(property: 'last_four', description: 'Last four digits', type: 'string', example: '1111'),
        new OAT\Property(property: 'expires_month', description: 'Expiration month', type: 'integer', example: 12),
        new OAT\Property(property: 'expires_year', description: 'Expiration year', type: 'integer', example: 2028),
        new OAT\Property(property: 'notes', description: 'Card notes', type: 'string', example: 'Company debit card'),
    ],
    type: 'object'
)]
class BankCard extends Model
{
    use HasFactory;
    use SoftDeletes;

    const string TYPE_DEBIT = 'debit';

    const string TYPE_CREDIT = 'credit';

    const string NETWORK_VISA = 'visa';

    const string NETWORK_MASTERCARD = 'mastercard';

    const string NETWORK_AMEX = 'amex';

    const string NETWORK_OTHER = 'other';

    const array NETWORKS = [
        self::NETWORK_VISA => 'Visa',
        self::NETWORK_MASTERCARD => 'Mastercard',
        self::NETWORK_AMEX => 'American Express',
        self::NETWORK_OTHER => 'Other',
    ];

    protected $table = 'bank_card';

    protected $fillable = [
        'company_id',
        'bank_account_id',
        'type',
        'network',
        'cardholder_name',
        'number_encrypted',
        'last_four',
        'cvv_encrypted',
        'expires_month',
        'expires_year',
        'notes',
    ];

    protected $hidden = [
        'number_encrypted',
        'cvv_encrypted',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function setNumberAttribute(string $number): void
    {
        $clean = preg_replace('/\D/', '', $number);
        $this->attributes['number_encrypted'] = Crypt::encryptString($clean);
        $this->attributes['last_four'] = substr($clean, -4);
    }

    public function getNumberAttribute(): string
    {
        return Crypt::decryptString($this->attributes['number_encrypted']);
    }

    public function getMaskedNumberAttribute(): string
    {
        // Shows first 4 and last 4, masks middle: 4111-****-****-1111
        $first4 = '****';

        if (! empty($this->attributes['number_encrypted'])) {
            $full = $this->getNumberAttribute();
            $first4 = substr($full, 0, 4);
        }

        return $this->network === self::NETWORK_AMEX
            ? $first4.' ****** '.$this->last_four
            : $first4.' **** **** '.$this->last_four;
    }

    public function setCvvAttribute(?string $cvv): void
    {
        $this->attributes['cvv_encrypted'] = $cvv ? Crypt::encryptString($cvv) : null;
    }

    public function getCvvAttribute(): ?string
    {
        if (empty($this->attributes['cvv_encrypted'])) {
            return null;
        }

        return Crypt::decryptString($this->attributes['cvv_encrypted']);
    }

    public function getExpiresFormattedAttribute(): string
    {
        return str_pad((string) $this->expires_month, 2, '0', STR_PAD_LEFT).'/'.$this->expires_year;
    }
}
