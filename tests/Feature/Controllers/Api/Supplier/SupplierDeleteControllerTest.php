<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Supplier;

use App\Models\Supplier;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupplierDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_a_supplier(): void
    {
        $this->actingAs($this->user, 'api');
        $supplier = Supplier::factory()->create(['company_id' => auth()->user()->company_id]);

        $response = $this->deleteJson('/api/supplier/'.$supplier->id);

        $response->assertOk();
        $this->assertSoftDeleted('supplier', ['id' => $supplier->id]);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_supplier(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->deleteJson('/api/supplier/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_delete_a_supplier_from_another_company(): void
    {
        $this->actingAs($this->user, 'api');
        $otherSupplier = Supplier::factory()->create(['company_id' => User::factory()->create()->company_id]);

        $response = $this->deleteJson('/api/supplier/'.$otherSupplier->id);

        $response->assertNotFound();
        $this->assertDatabaseHas('supplier', ['id' => $otherSupplier->id, 'deleted_at' => null]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->deleteJson('/api/supplier/'.$supplier->id);

        $response->assertUnauthorized();
    }
}
