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
}
