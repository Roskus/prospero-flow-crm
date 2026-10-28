<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Squire\Models\Currency;

class CurrencyHelper
{
    /**
     * Largest absolute value that fits the money columns, decimal(18,5): 13 integer digits and 5 decimals.
     */
    public const string MAX_AMOUNT = '9999999999999.99999';

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

    /**
     * Formats an amount with the symbol of the given currency, or its code when it has no symbol.
     */
    public static function formatIn(float|int|string $amount, string $currency): string
    {
        $symbol = Currency::find(strtolower($currency))?->getAttribute('symbol') ?: strtoupper($currency);

        return $symbol.' '.number_format((float) $amount, 2);
    }

    /**
     * Upper case ISO codes of every known currency, to validate currency inputs.
     *
     * @return list<string>
     */
    public static function codes(): array
    {
        return once(fn (): array => Currency::query()->pluck('id')->map(fn (string $code): string => strtoupper($code))->values()->all());
    }
}
