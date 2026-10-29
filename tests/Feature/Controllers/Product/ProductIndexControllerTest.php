<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Product;

use App\Models\Company;
use App\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductIndexControllerTest extends TestCase
{
    #[Test]
    public function it_lists_the_ean_after_the_sku(): void
    {
        Product::factory()->create([
            'company_id' => $this->user->company_id,
            'sku' => 'SKU-001',
            'barcode' => '8412345678905',
        ]);

        $response = $this->get('/product');

        $response->assertOk();
        $response->assertSeeInOrder(['<th>SKU</th>', '<th>EAN</th>'], false);
        $response->assertSeeInOrder(['SKU-001', '8412345678905']);
    }

    #[Test]
    public function it_lists_the_most_recently_updated_products_first(): void
    {
        Product::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Zzolder product', 'updated_at' => now()->subDays(2)]);
        Product::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Zznewer product', 'updated_at' => now()]);

        $this->get('/product')
            ->assertOk()
            ->assertSeeInOrder(['Zznewer product', 'Zzolder product']);
    }

    #[Test]
    public function the_autocomplete_finds_products_by_name_sku_or_ean_of_the_company_only(): void
    {
        $product = Product::factory()->create([
            'company_id' => $this->user->company_id,
            'name' => 'Zzbook',
            'sku' => 'ZZSKU-1',
            'barcode' => '8412345678905',
        ]);
        Product::factory()->create([
            'company_id' => Company::factory()->create()->id,
            'name' => 'Zzbook other company',
            'sku' => 'ZZSKU-1',
            'barcode' => '8412345678905',
        ]);

        foreach (['Zzbook', 'ZZSKU-1', '8412345678905'] as $term) {
            $this->getJson('/product?term='.$term, ['X-Requested-With' => 'XMLHttpRequest'])
                ->assertOk()
                ->assertJsonCount(1)
                ->assertJsonPath('0.id', $product->id);
        }
    }
}
