<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Currency;

use App\Models\CompanyCurrencyRate;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CurrencyIndexControllerTest extends TestCase
{
    #[Test]
    public function it_can_render_the_currency_settings_screen(): void
    {
        $this->user->company->update(['currency' => 'EUR']);

        CompanyCurrencyRate::create([
            'company_id' => $this->user->company_id,
            'currency' => 'USD',
            'conversion_rate' => 1.120000,
        ]);

        $response = $this->get('/currency');

        $response->assertOk();
        $response->assertSeeText('EUR');
        $response->assertSee('value="1.120000"', false);
        $response->assertSee('action="'.route('currency.save').'"', false);
    }

    #[Test]
    public function each_rate_shows_its_direction_to_the_company_currency(): void
    {
        $this->user->company->update(['currency' => 'ARS']);

        $this->get('/currency')
            ->assertOk()
            ->assertSeeText('Exchange rate')
            ->assertSeeInOrder(['1 USD =', 'name="conversion_rates[USD]"', 'ARS'], false)
            ->assertSeeText('Enter how many ARS one unit of each currency is worth.');
    }
}
