<?php

declare(strict_types=1);

namespace App\Models\Bank;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Squire\Models\Country;

class Account extends Model
{
    protected $table = 'bank_account';

    protected $fillable = [
        'country_id',
        'bank_id',
        'currency',
        'iban',
        'amount',
        'notes',
    ];

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
