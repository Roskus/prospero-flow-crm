<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Brand;

use App\Models\Brand;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrandUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_can_update_a_brand(): void
    {
        $this->actingAs($this->user, 'api');
        $brand = Brand::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->putJson('/api/brand/'.$brand->id, ['name' => 'Updated Brand']);

        $response->assertOk();
        $this->assertDatabaseHas('brand', ['id' => $brand->id, 'name' => 'Updated Brand']);
    }

    #[Test]
    public function it_cannot_update_a_brand_from_another_company(): void
    {
        $this->actingAs($this->user, 'api');
        $otherUser = User::factory()->create();
        $otherBrand = Brand::factory()->create(['company_id' => $otherUser->company_id]);

        $response = $this->putJson('/api/brand/'.$otherBrand->id, ['name' => 'Hacked']);

        $response->assertNotFound();
        $this->assertDatabaseMissing('brand', ['id' => $otherBrand->id, 'name' => 'Hacked']);
    }

    #[Test]
    public function it_validates_required_name(): void
    {
        $this->actingAs($this->user, 'api');
        $brand = Brand::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->putJson('/api/brand/'.$brand->id, []);

        $response->assertUnprocessable();
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->putJson('/api/brand/'.$brand->id, ['name' => 'Updated']);

        $response->assertUnauthorized();
    }
}
