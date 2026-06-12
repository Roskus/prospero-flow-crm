<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Currency;

use App\Models\CompanyCurrencyRate;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CurrencySaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_update_currency_conversion_rates_for_the_current_company(): void
    {
        $this->user->company->update(['currency' => 'EUR']);

        $response = $this->post('/currency/save', [
            'conversion_rates' => [
                'EUR' => '1.000000',
                'USD' => '1.120000',
                'GBP' => '0.860000',
            ],
        ]);

        $response->assertRedirect('/currency');
        $this->assertDatabaseHas('company_currency_rate', [
            'company_id' => $this->user->company_id,
            'currency' => 'USD',
            'conversion_rate' => '1.120000',
        ]);
        $this->assertDatabaseHas('company_currency_rate', [
            'company_id' => $this->user->company_id,
            'currency' => 'EUR',
            'conversion_rate' => '1.000000',
        ]);
    }

    #[Test]
    public function it_rejects_unknown_currency_codes(): void
    {
        $response = $this->from('/currency')->post('/currency/save', [
            'conversion_rates' => [
                'EUR' => '1.000000',
                'ZZZ' => '99.000000',
            ],
        ]);

        $response->assertRedirect('/currency');
        $response->assertSessionHasErrors('conversion_rates.ZZZ');
        $this->assertSame(0, CompanyCurrencyRate::count());
    }
}
