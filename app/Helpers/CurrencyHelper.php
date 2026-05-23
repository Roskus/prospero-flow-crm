<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Squire\Models\Currency;

class CurrencyHelper
{
    public static function symbol(): string
    {
        $user = Auth::user();
        if (! $user || ! $user->company) {
            return '';
        }

        $currency = Currency::find(strtolower((string) $user->company->currency));

        return $currency?->symbol ?? '';
    }

    public static function format(float|int|string $amount): string
    {
        $symbol = self::symbol();
        $formatted = number_format((float) $amount, 2);

        return $symbol !== '' ? "{$symbol} {$formatted}" : $formatted;
    }
}
