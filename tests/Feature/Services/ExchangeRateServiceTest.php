<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Helpers\CurrencyHelper;
use App\Models\Company;
use App\Services\ExchangeRateService;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    private ExchangeRateService $exchangeRateService;

    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exchangeRateService = app(ExchangeRateService::class);
        $this->company = Company::factory()->create(['currency' => 'ARS']);
        $this->company->currencyRates()->createMany([
            ['currency' => 'USD', 'conversion_rate' => 1200],
            ['currency' => 'EUR', 'conversion_rate' => 1300],
        ]);
    }

    #[Test]
    public function the_company_currency_rate_is_one(): void
    {
        $this->assertSame('1.000000', $this->exchangeRateService->rate($this->company, 'ars'));
    }

    #[Test]
    public function it_returns_the_stored_rate(): void
    {
        $this->assertSame('1200.000000', $this->exchangeRateService->rate($this->company, 'USD'));
    }

    #[Test]
    public function it_converts_a_foreign_amount_to_the_company_currency(): void
    {
        $this->assertSame('12000.00', $this->exchangeRateService->convert('10', 'USD', 'ARS', $this->company));
        $this->assertSame('10.00', $this->exchangeRateService->convert('12000', 'ARS', 'USD', $this->company));
    }

    #[Test]
    public function it_converts_between_two_foreign_currencies(): void
    {
        $this->assertSame('110.77', $this->exchangeRateService->convert('120', 'USD', 'EUR', $this->company));
    }

    #[Test]
    public function a_missing_rate_is_a_validation_error_on_the_given_attribute(): void
    {
        try {
            $this->exchangeRateService->convert('10', 'BRL', 'ARS', $this->company);
            $this->fail('A missing rate must fail');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('currency', $exception->errors());
            $this->assertStringContainsString('BRL', $exception->errors()['currency'][0]);
        }

        $this->expectException(ValidationException::class);
        $this->exchangeRateService->rate($this->company, 'BRL', 'payment_currency');
    }

    #[Test]
    public function it_rounds_money_half_away_from_zero(): void
    {
        $this->assertSame('2.35', ExchangeRateService::round('2.345'));
        $this->assertSame('-2.35', ExchangeRateService::round('-2.345'));
    }

    #[Test]
    public function the_currency_helper_formats_in_a_given_currency_and_lists_the_codes(): void
    {
        $this->assertSame('$ 1,200.50', CurrencyHelper::formatIn('1200.5', 'USD'));
        $this->assertContains('ARS', CurrencyHelper::codes());
        $this->assertNotContains('ars', CurrencyHelper::codes());
    }
}
