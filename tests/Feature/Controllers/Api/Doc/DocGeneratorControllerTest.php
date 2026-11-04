<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Doc;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocGeneratorControllerTest extends TestCase
{
    #[Test]
    public function it_renders_the_openapi_document(): void
    {
        $response = $this->get('/api/resource.json');

        $response->assertOk();
        $document = json_decode($response->json(), true);
        $this->assertArrayHasKey('Lead', $document['components']['schemas']);
        $this->assertArrayHasKey('Contact', $document['components']['schemas']);
    }

    #[Test]
    public function the_address_schema_is_shared_by_the_entities_with_an_address(): void
    {
        $document = json_decode($this->get('/api/resource.json')->json(), true);
        $schemas = $document['components']['schemas'];

        $this->assertSame(
            ['country_id', 'province', 'city', 'locality', 'street', 'address_extra', 'zipcode', 'latitude', 'longitude'],
            array_keys($schemas['Address']['properties'])
        );

        foreach (['Lead', 'Customer', 'Company', 'Branch', 'Contact', 'Supplier', 'SupplierContact'] as $schema) {
            $this->assertContains(['$ref' => '#/components/schemas/Address'], $schemas[$schema]['allOf'] ?? [], $schema);
            $this->assertArrayNotHasKey('country_id', $schemas[$schema]['properties'] ?? [], $schema);
        }
    }
}
