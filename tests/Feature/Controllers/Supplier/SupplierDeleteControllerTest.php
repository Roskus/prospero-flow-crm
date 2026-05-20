<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Supplier;

use App\Models\Company;
use App\Models\Supplier;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupplierDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/supplier/delete/'.$supplier->id);

        $response->assertRedirect('/supplier');
        $this->assertDatabaseMissing('supplier', ['id' => $supplier->id]);
    }

    #[Test]
    public function it_returns_404_when_supplier_not_found(): void
    {
        $response = $this->get('/supplier/delete/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_delete_supplier_from_another_company(): void
    {
        $otherCompany = Company::factory()->create();
        $supplier = Supplier::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->get('/supplier/delete/'.$supplier->id);

        $response->assertNotFound();
        $this->assertDatabaseHas('supplier', ['id' => $supplier->id]);
    }
}
