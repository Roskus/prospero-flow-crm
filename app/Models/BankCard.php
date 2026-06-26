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

#[OAT\Schema(schema: 'BankCard', required: ['bank_account_id', 'type', 'network', 'cardholder_name', 'number', 'expires_month', 'expires_year'], type: 'object')]
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

    #[OAT\Property(property: 'bank_account_id', type: 'integer', example: 1)]
    #[OAT\Property(property: 'type', type: 'string', example: 'debit', enum: ['debit', 'credit'])]
    #[OAT\Property(property: 'network', type: 'string', example: 'visa', enum: ['visa', 'mastercard', 'amex', 'other'])]
    #[OAT\Property(property: 'cardholder_name', type: 'string', example: 'John Doe', maxLength: 80)]
    #[OAT\Property(property: 'number', type: 'string', example: '4111111111111111')]
    #[OAT\Property(property: 'cvv', type: 'string', example: '123', nullable: true, maxLength: 4)]
    #[OAT\Property(property: 'expires_month', type: 'integer', example: 12)]
    #[OAT\Property(property: 'expires_year', type: 'integer', example: 2028)]
    #[OAT\Property(property: 'last_four', type: 'string', example: '1111')]
    #[OAT\Property(property: 'notes', type: 'string', example: 'Company debit card', nullable: true)]
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
        try {
            $full = $this->getNumberAttribute();
            $first4 = substr($full, 0, 4);
        } catch (\Exception) {
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
