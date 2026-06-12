<?php

declare(strict_types=1);

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\MainController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Squire\Models\Currency;

class CurrencyIndexController extends MainController
{
    /**
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $company = $request->user()?->company;

        abort_if($company === null, 404);

        return view('currency.index', [
            'company' => $company,
            'baseCurrency' => strtoupper((string) $company->currency),
            'currencies' => Currency::all()->sortBy('name')->values(),
            'currencyRates' => $company->currencyRates()
                ->pluck('conversion_rate', 'currency'),
        ]);
    }
}
