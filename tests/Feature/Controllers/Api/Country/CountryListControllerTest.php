<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Country;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CountryListControllerTest extends TestCase
{
    #[Test]
    public function it_lists_the_countries_of_the_library_in_the_requested_language(): void
    {
        $this->actingAs($this->user, 'api');

        $english = $this->getJson('/api/country?lang=en')->assertOk();
        $spanish = $this->getJson('/api/country?lang=es')->assertOk();

        $this->assertSame(250, $english->json('count'));
        $this->assertCount(250, $english->json('countries'));
        $this->assertContains(['id' => 'es', 'name' => 'Spain'], $english->json('countries'));
        $this->assertContains(['id' => 'es', 'name' => 'España'], $spanish->json('countries'));
        $this->assertContains(['id' => 'de', 'name' => 'Alemania'], $spanish->json('countries'));
    }

    #[Test]
    public function it_sorts_the_countries_by_name(): void
    {
        $this->actingAs($this->user, 'api');

        $names = array_column($this->getJson('/api/country?lang=en')->json('countries'), 'name');
        $sorted = $names;
        natcasesort($sorted);

        $this->assertSame(array_values($sorted), $names);
        $this->assertSame('Afghanistan', $names[0]);
    }

    #[Test]
    public function it_uses_the_application_locale_by_default(): void
    {
        $this->actingAs($this->user, 'api');
        app()->setLocale('es');

        $this->getJson('/api/country')->assertOk()->assertJsonFragment(['id' => 'es', 'name' => 'España']);
    }

    #[Test]
    public function it_rejects_a_language_the_library_does_not_have(): void
    {
        $this->actingAs($this->user, 'api');

        $this->getJson('/api/country?lang=xx')->assertUnprocessable()->assertJsonValidationErrors('lang');
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $this->getJson('/api/country')->assertUnauthorized();
    }
}
