<?php

declare(strict_types=1);

namespace App\Models\Account;

use App\Models\Account;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['account_id', 'amount', 'currency', 'description', 'type'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
