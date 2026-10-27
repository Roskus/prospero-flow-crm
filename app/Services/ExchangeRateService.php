<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use Illuminate\Validation\ValidationException;
use RoundingMode;

/**
 * A rate is the number of units of the company currency that one unit of the currency is worth.
 */
class ExchangeRateService
{
    private const int SCALE = 6;

    /**
     * @throws ValidationException when the company has no rate for the currency
     */
    public function rate(Company $company, string $currency, string $attribute = 'currency'): string
    {
        $currency = strtoupper($currency);

        if ($currency === strtoupper((string) $company->currency)) {
            return '1.000000';
        }

        $rate = $company->currencyRates()->where('currency', $currency)->value('conversion_rate');

        if ($rate === null || bccomp((string) $rate, '0', self::SCALE) <= 0) {
            throw ValidationException::withMessages([
                $attribute => __('Set the exchange rate of :currency in the currency settings first.', ['currency' => $currency]),
            ]);
        }

        return bcadd((string) $rate, '0', self::SCALE);
    }

    /**
     * Converts an amount between two currencies of the company, rounded to 2 decimals.
     *
     * @throws ValidationException when the company has no rate for one of the currencies
     */
    public function convert(string $amount, string $from, string $to, Company $company): string
    {
        if (strtoupper($from) === strtoupper($to)) {
            return self::round($amount);
        }

        $inCompanyCurrency = bcmul($amount, $this->rate($company, $from), self::SCALE + 4);

        return self::round(bcdiv($inCompanyCurrency, $this->rate($company, $to), self::SCALE + 4));
    }

    /**
     * Rounds half away from zero to the given decimals.
     */
    public static function round(string $amount, int $decimals = 2): string
    {
        return bcround($amount, $decimals, RoundingMode::HalfAwayFromZero);
    }
}
