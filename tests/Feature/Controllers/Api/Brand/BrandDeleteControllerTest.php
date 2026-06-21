<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Brand;

use App\Models\Brand;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrandDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_a_brand(): void
    {
        $this->actingAs($this->user, 'api');
        $brand = Brand::factory()->create(['company_id' => auth()->user()->company_id]);

        $response = $this->deleteJson('/api/brand/'.$brand->id);

        $response->assertOk();
        $this->assertSoftDeleted('brand', ['id' => $brand->id]);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_brand(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->deleteJson('/api/brand/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_delete_a_brand_from_another_company(): void
    {
        $this->actingAs($this->user, 'api');
        $otherBrand = Brand::factory()->create(['company_id' => User::factory()->create()->company_id]);

        $response = $this->deleteJson('/api/brand/'.$otherBrand->id);

        $response->assertNotFound();
        $this->assertDatabaseHas('brand', ['id' => $otherBrand->id, 'deleted_at' => null]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->deleteJson('/api/brand/'.$brand->id);

        $response->assertUnauthorized();
    }
}
