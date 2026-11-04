<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Doc;

use PHPUnit\Framework\Attributes\DataProvider;
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

    /**
     * @return array<string, array{string}>
     */
    public static function listPaths(): array
    {
        return array_combine(self::LIST_PATHS, array_map(fn (string $path): array => [$path], self::LIST_PATHS));
    }

    private const array LIST_PATHS = [
        '/bank-account', '/bank-card', '/brand', '/calendar', '/company', '/contact', '/customer',
        '/email', '/lead', '/order', '/product', '/supplier', '/ticket', '/transaction', '/user',
    ];

    #[Test]
    #[DataProvider('listPaths')]
    public function a_list_endpoint_documents_the_fields_it_returns_with_an_array_of_results(string $path): void
    {
        $document = json_decode($this->get('/api/resource.json')->json(), true);
        $schema = $document['paths'][$path]['get']['responses']['200']['content']['application/json']['schema'];
        $properties = $schema['properties'] ?? [];
        foreach ($schema['allOf'] ?? [] as $part) {
            $resolved = isset($part['$ref'])
                ? $document['components']['schemas'][substr($part['$ref'], strlen('#/components/schemas/'))]
                : $part;
            $properties = [...$properties, ...$resolved['properties']];
        }

        $arrays = array_filter($properties, fn (array $property): bool => ($property['type'] ?? null) === 'array' && isset($property['items']['$ref']));
        $this->assertCount(1, $arrays, $path);

        $response = $this->actingAs($this->user, 'api')->getJson('/api'.$path);

        $response->assertOk();
        $this->assertEqualsCanonicalizing(array_keys($properties), array_keys($response->json()), $path);
    }
}
