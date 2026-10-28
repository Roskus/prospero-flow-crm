<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Product;

use App\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductCurrencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user->company->update(['currency' => 'ARS']);
    }

    #[Test]
    public function the_new_product_form_defaults_to_the_company_currency(): void
    {
        $this->get('/product/create')
            ->assertOk()
            ->assertSee('<option value="ARS" selected>', false);
    }

    #[Test]
    public function a_product_can_be_priced_in_another_currency(): void
    {
        $this->post('/product/save', $this->payload(['currency' => 'usd']))->assertRedirect('/product');

        $product = $this->latestProduct();
        $this->assertSame('USD', $product->currency);

        $this->get('/product')->assertOk()->assertSeeText('50,00 USD');
    }

    #[Test]
    public function a_product_without_currency_gets_the_company_currency_and_keeps_it_on_edit(): void
    {
        $this->post('/product/save', $this->payload())->assertRedirect('/product');
        $product = $this->latestProduct();
        $this->assertSame('ARS', $product->currency);

        $product->update(['currency' => 'USD']);
        $this->post('/product/save', $this->payload(['id' => $product->id]))->assertRedirect('/product');

        $this->assertSame('USD', $product->fresh()->currency);
    }

    #[Test]
    public function an_unknown_currency_is_rejected(): void
    {
        $this->post('/product/save', $this->payload(['currency' => 'XXY']))->assertSessionHasErrors('currency');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        $product = Product::factory()->make(['company_id' => $this->user->company_id]);

        return array_merge([
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'name' => 'Producto de prueba',
            'cost' => 10,
            'price' => 50,
            'quantity' => 1,
            'min_stock_quantity' => 0,
        ], $overrides);
    }

    private function latestProduct(): Product
    {
        return Product::where('company_id', $this->user->company_id)->latest('id')->firstOrFail();
    }
}
