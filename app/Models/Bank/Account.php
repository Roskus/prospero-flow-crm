<?php

declare(strict_types=1);

namespace App\Models\Bank;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OAT;
use Squire\Models\Country;

#[OAT\Schema(schema: 'BankAccount', required: ['bank_id', 'country_id', 'currency', 'iban', 'amount'], type: 'object')]
class Account extends Model
{
    use HasFactory;

    protected $table = 'bank_account';

    protected $fillable = [
        'company_id',
        'country_id',
        'bank_id',
        'currency',
        'iban',
        'amount',
        'notes',
    ];

    #[OAT\Property(property: 'bank_id', type: 'integer', example: 1)]
    #[OAT\Property(property: 'country_id', type: 'string', example: 'ES')]
    #[OAT\Property(property: 'currency', type: 'string', example: 'EUR')]
    #[OAT\Property(property: 'iban', type: 'string', example: 'ES9121000418450200051332')]
    #[OAT\Property(property: 'amount', type: 'number', format: 'float', example: 1500.00)]
    #[OAT\Property(property: 'notes', type: 'string', example: 'Main operating account')]
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function getAllByCompanyId(int $companyId)
    {
        return Account::where('company_id', $companyId)->get();
    }
}
