<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Product;

use App\Models\Product;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductListControllerTest extends TestCase
{
    #[Test]
    public function it_lists_the_company_products_most_recently_updated_first(): void
    {
        $this->actingAs($this->user, 'api');
        $older = Product::factory()->create(['company_id' => $this->user->company_id, 'updated_at' => now()->subDays(2)]);
        $newer = Product::factory()->create(['company_id' => $this->user->company_id, 'updated_at' => now()]);
        Product::factory()->create(['company_id' => User::factory()->create()->company_id]);

        $response = $this->getJson('/api/product');

        $response->assertOk()
            ->assertJsonPath('count', 2)
            ->assertJsonPath('products.0.id', $newer->id)
            ->assertJsonPath('products.1.id', $older->id);
    }
}
