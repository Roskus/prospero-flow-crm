<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyCurrencyRate extends Model
{
    protected $table = 'company_currency_rate';

    protected $fillable = [
        'company_id',
        'currency',
        'conversion_rate',
    ];

    protected function casts(): array
    {
        return [
            'conversion_rate' => 'decimal:6',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
