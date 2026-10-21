<?php

declare(strict_types=1);

namespace Tests\Feature\Traits;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Supplier\SupplierContact;
use App\ValueObjects\Address;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HasAddressTest extends TestCase
{
    #[Test]
    public function it_reads_the_address_from_the_columns(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'country_id' => 'ar',
            'city' => 'Rosario',
            'street' => 'Córdoba 1234',
            'address_extra' => 'Piso 3 B',
        ]);

        $address = $lead->fresh()->address;

        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('Rosario', $address->city);
        $this->assertSame('Córdoba 1234', $address->street);
        $this->assertSame('Piso 3 B', $address->extra);
    }

    #[Test]
    public function assigning_the_address_writes_every_column(): void
    {
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id, 'locality' => 'Palermo']);

        $lead->address = Address::fromArray([
            'country_id' => 'es',
            'province' => 'Madrid',
            'city' => 'Madrid',
            'street' => 'Calle Mayor 1',
            'address_extra' => '2º A',
            'zipcode' => '28001',
            'latitude' => '40.4167',
            'longitude' => '-3.70325',
        ]);
        $lead->save();

        $this->assertDatabaseHas('lead', [
            'id' => $lead->id,
            'country_id' => 'es',
            'province' => 'Madrid',
            'city' => 'Madrid',
            'locality' => null,
            'street' => 'Calle Mayor 1',
            'address_extra' => '2º A',
            'zipcode' => '28001',
        ]);
        $this->assertEqualsWithDelta(40.4167, (float) $lead->fresh()->latitude, 0.0001);
    }

    #[Test]
    public function changing_a_column_directly_is_not_overwritten_by_a_previous_read(): void
    {
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id, 'city' => 'Rosario']);

        $this->assertSame('Rosario', $lead->address->city);
        $lead->city = 'Córdoba';
        $lead->save();

        $this->assertSame('Córdoba', $lead->fresh()->city);
        $this->assertSame('Córdoba', $lead->address->city);
    }

    #[Test]
    public function the_address_is_not_added_to_the_serialized_model(): void
    {
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);

        $this->assertArrayNotHasKey('address', $lead->toArray());
    }

    #[Test]
    public function contacts_store_an_optional_address(): void
    {
        $contact = Contact::factory()->create(['company_id' => $this->user->company_id]);
        $supplierContact = SupplierContact::factory()->create(['company_id' => $this->user->company_id]);

        $this->assertTrue($contact->address->isEmpty());

        $contact->address = Address::fromArray(['country_id' => 'es', 'city' => 'Madrid']);
        $contact->save();
        $supplierContact->address = Address::fromArray(['country_id' => 'ar', 'city' => 'Rosario']);
        $supplierContact->save();

        $this->assertSame('Madrid, Spain', $contact->fresh()->address->oneLine());
        $this->assertSame('Rosario, Argentina', $supplierContact->fresh()->address->oneLine());
    }

    #[Test]
    public function supplier_contact_attributes_are_not_shadowed_by_class_properties(): void
    {
        $supplierContact = SupplierContact::factory()->create([
            'company_id' => $this->user->company_id,
            'first_name' => 'Jane',
        ]);

        $this->assertSame('Jane', $supplierContact->fresh()->first_name);
        $this->assertSame('Jane', $supplierContact->fresh()->toArray()['first_name']);
    }
}
