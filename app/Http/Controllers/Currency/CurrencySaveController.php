<?php

declare(strict_types=1);

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\MainController;
use App\Http\Requests\CurrencySettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CurrencySaveController extends MainController
{
    public function save(CurrencySettingsRequest $request): RedirectResponse
    {
        $company = $request->user()?->company;

        abort_if($company === null, 404);

        $baseCurrency = strtoupper((string) $company->currency);
        $conversionRates = collect($request->validated()['conversion_rates'] ?? []);

        DB::transaction(function () use ($baseCurrency, $company, $conversionRates): void {
            $company->currencyRates()->delete();

            $conversionRates->each(function (mixed $rate, string|int $currency) use ($baseCurrency, $company): void {
                $currencyCode = strtoupper((string) $currency);

                if ($currencyCode !== $baseCurrency && ($rate === null || $rate === '')) {
                    return;
                }

                $company->currencyRates()->create([
                    'currency' => $currencyCode,
                    'conversion_rate' => $currencyCode === $baseCurrency ? 1 : $rate,
                ]);
            });
        });

        return redirect('/currency')->with([
            'status' => 'success',
            'message' => 'Currency conversion rates saved successfully',
        ]);
    }
}
