<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Product;

use App\Models\Product;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_a_product(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $product = Product::factory()->create(['company_id' => auth()->user()->company_id]);

        $response = $this->deleteJson('/api/product/'.$product->id);

        $response->assertOk();
        $this->assertSoftDeleted($product);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_product(): void
    {
        $this->actingAs(User::factory()->create(), 'api');

        $response = $this->deleteJson('/api/product/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_delete_a_product_from_another_company(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $otherProduct = Product::factory()->create(['company_id' => User::factory()->create()->company_id]);

        $response = $this->deleteJson('/api/product/'.$otherProduct->id);

        $response->assertNotFound();
        $this->assertNotSoftDeleted($otherProduct);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson('/api/product/'.$product->id);

        $response->assertUnauthorized();
    }
}
