<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Supplier;

use App\Models\Supplier;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupplierCreateControllerTest extends TestCase
{
    #[Test]
    public function it_creates_a_supplier_with_the_full_address(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/supplier', [
            'name' => 'Sony',
            'country' => 'GB',
            'province' => 'London',
            'city' => 'London',
            'locality' => 'Westminster',
            'street' => '123 Main St',
            'address_extra' => 'Floor 2',
            'zipcode' => 'W1A 1AA',
            'latitude' => 51.5072,
            'longitude' => -0.1276,
        ]);

        $response->assertCreated();
        $supplier = Supplier::findOrFail($response->json('supplier.id'));
        $this->assertSame('gb', $supplier->country_id);
        $this->assertSame('London', $supplier->city);
        $this->assertSame('Westminster', $supplier->locality);
        $this->assertSame('Floor 2', $supplier->address_extra);
        $this->assertTrue($supplier->address->hasCoordinates());
    }

    #[Test]
    public function it_rejects_a_province_longer_than_80_characters(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/supplier', [
            'name' => 'Sony',
            'country' => 'gb',
            'province' => str_repeat('a', 81),
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('province');
    }

    #[Test]
    public function it_rejects_an_unknown_country(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/supplier', ['name' => 'Sony', 'country' => 'UK']);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('country');
    }

    #[Test]
    public function update_keeps_the_address_fields_not_sent(): void
    {
        $this->actingAs($this->user, 'api');
        $supplier = Supplier::factory()->create([
            'company_id' => $this->user->company_id,
            'city' => 'Madrid',
            'street' => 'Calle Mayor 1',
        ]);

        $response = $this->putJson('/api/supplier/'.$supplier->id, [
            'name' => 'Renamed',
            'country' => 'ES',
            'zipcode' => '28013',
        ]);

        $response->assertOk();
        $supplier->refresh();
        $this->assertSame('Renamed', $supplier->name);
        $this->assertSame('es', $supplier->country_id);
        $this->assertSame('28013', $supplier->zipcode);
        $this->assertSame('Madrid', $supplier->city);
        $this->assertSame('Calle Mayor 1', $supplier->street);
    }
}
