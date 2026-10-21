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

    #[Test]
    public function it_saves_the_full_address(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/supplier/save', [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'country_id' => 'ar',
            'province' => 'Santa Fe',
            'city' => 'Rosario',
            'locality' => 'Centro',
            'street' => 'Córdoba 1234',
            'address_extra' => 'Piso 3 B',
            'zipcode' => '2000',
            'latitude' => '-32.9468',
            'longitude' => '-60.6393',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('supplier', [
            'id' => $supplier->id,
            'city' => 'Rosario',
            'locality' => 'Centro',
            'address_extra' => 'Piso 3 B',
            'latitude' => -32.9468,
        ]);
    }

    #[Test]
    public function it_rejects_a_province_longer_than_80_characters(): void
    {
        $supplier = Supplier::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/supplier/save', [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'country_id' => 'es',
            'province' => str_repeat('a', 81),
        ]);

        $response->assertSessionHasErrors('province');
    }
}
