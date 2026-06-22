<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Supplier;

use App\Models\Supplier;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupplierSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_supplier(): void
    {
        $data = Supplier::factory()->create(['company_id' => $this->user->company_id])->toArray();

        $response = $this->post('/supplier/save', $data);

        $response->assertRedirect('/supplier');
    }

    #[Test]
    public function it_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/supplier/save', [
            'id' => $supplier->id,
            'name' => 'Updated supplier',
            'country_id' => 'ES',
        ]);

        $response->assertRedirect('/supplier');
        $this->assertDatabaseHas('supplier', [
            'id' => $supplier->id,
            'name' => 'Updated supplier',
        ]);
    }
}
