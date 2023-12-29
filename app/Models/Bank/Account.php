<?php

declare(strict_types=1);

namespace App\Models\Bank;

use Illuminate\Database\Eloquent\Model;

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
}
