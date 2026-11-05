<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Contact;

use App\Models\Contact;
use App\Models\Customer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContactListControllerTest extends TestCase
{
    private const string SEARCH_BY_LINKEDIN = '/api/contact?linkedin=';

    private const string JANE_DOE_URL = 'https://www.linkedin.com/in/jane-doe';

    #[Test]
    public function it_lists_the_contacts_of_the_company(): void
    {
        $this->actingAs($this->user, 'api');
        Contact::factory()->count(2)->create(['company_id' => $this->user->company_id]);
        Contact::factory()->create(['company_id' => $this->user->company_id + 1]);

        $this->getJson('/api/contact')->assertOk()->assertJsonPath('count', 2)->assertJsonCount(2, 'contacts');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function sameProfileUrls(): array
    {
        return [
            'canonical' => [self::JANE_DOE_URL],
            'without www and with trailing slash' => ['https://linkedin.com/in/jane-doe/'],
            'without scheme and with capitals' => ['linkedin.com/in/Jane-Doe'],
            'with query' => ['https://www.linkedin.com/in/jane-doe?trk=public_profile'],
        ];
    }

    #[Test]
    #[DataProvider('sameProfileUrls')]
    public function it_finds_a_contact_by_linkedin_url_however_it_is_written(string $searchedUrl): void
    {
        $this->actingAs($this->user, 'api');
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $contact = Contact::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => $customer->id,
            'lead_id' => null,
            'linkedin' => 'https://www.linkedin.com/in/jane-doe/',
        ]);
        Contact::factory()->create(['company_id' => $this->user->company_id, 'linkedin' => 'https://www.linkedin.com/in/jane-doe-2']);
        Contact::factory()->create(['company_id' => $this->user->company_id, 'linkedin' => null]);

        $response = $this->getJson(self::SEARCH_BY_LINKEDIN.urlencode($searchedUrl));

        $response->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('contacts.0.id', $contact->id)
            ->assertJsonPath('contacts.0.customer_id', $customer->id)
            ->assertJsonPath('contacts.0.lead_id', null);
    }

    #[Test]
    public function stored_urls_with_query_or_fragment_are_found(): void
    {
        $this->actingAs($this->user, 'api');
        Contact::factory()->create(['company_id' => $this->user->company_id, 'linkedin' => 'https://www.linkedin.com/in/jane-doe?trk=x']);
        Contact::factory()->create(['company_id' => $this->user->company_id, 'linkedin' => 'https://www.linkedin.com/in/jane-doe#about']);

        $this->getJson(self::SEARCH_BY_LINKEDIN.urlencode(self::JANE_DOE_URL))->assertOk()->assertJsonPath('count', 2);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function decodedAndEncodedUrls(): array
    {
        $decoded = 'https://www.linkedin.com/in/martín-gómez-pmp®-1a2b3c4d';
        $encoded = 'https://www.linkedin.com/in/mart%C3%ADn-g%C3%B3mez-pmp%C2%AE-1a2b3c4d/';

        return [
            'stored encoded, searched decoded' => [$encoded, $decoded],
            'stored encoded, searched encoded' => [$encoded, $encoded],
            'stored decoded, searched encoded' => [$decoded, $encoded],
        ];
    }

    #[Test]
    #[DataProvider('decodedAndEncodedUrls')]
    public function profile_names_with_accents_or_symbols_are_found_encoded_or_not(string $storedUrl, string $searchedUrl): void
    {
        $this->actingAs($this->user, 'api');
        $contact = Contact::factory()->create(['company_id' => $this->user->company_id, 'linkedin' => $storedUrl]);

        $this->getJson(self::SEARCH_BY_LINKEDIN.urlencode($searchedUrl))
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('contacts.0.id', $contact->id);
    }

    #[Test]
    public function no_contact_with_that_profile_returns_an_empty_list(): void
    {
        $this->actingAs($this->user, 'api');
        Contact::factory()->create(['company_id' => $this->user->company_id, 'linkedin' => self::JANE_DOE_URL]);

        $this->getJson(self::SEARCH_BY_LINKEDIN.urlencode('https://www.linkedin.com/in/nobody'))
            ->assertOk()
            ->assertJsonPath('count', 0)
            ->assertJsonCount(0, 'contacts');
    }

    #[Test]
    public function contacts_of_another_company_are_not_found(): void
    {
        $this->actingAs($this->user, 'api');
        Contact::factory()->create(['company_id' => $this->user->company_id + 1, 'linkedin' => self::JANE_DOE_URL]);

        $this->getJson(self::SEARCH_BY_LINKEDIN.urlencode(self::JANE_DOE_URL))->assertOk()->assertJsonPath('count', 0);
    }

    #[Test]
    public function a_url_that_is_not_a_linkedin_profile_is_rejected(): void
    {
        $this->actingAs($this->user, 'api');

        $this->getJson(self::SEARCH_BY_LINKEDIN.urlencode('https://www.linkedin.com/company/acme'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['linkedin']);
    }
}
