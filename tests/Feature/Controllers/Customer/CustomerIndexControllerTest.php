<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerIndexControllerTest extends TestCase
{
    #[Test]
    public function it_can_view_index_customers()
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id, 'status' => Customer::OPEN]);
        $customer2 = Customer::factory()->create(['company_id' => $this->user->company_id, 'status' => Customer::CLOSED]);

        $response = $this->get('/customer');
        $response->assertOk();
        $response->assertSee($customer->name);
        $response->assertSee($customer2->name);

        // SEARCH
        $response = $this->get('/customer?search='.$customer->name);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        // FILTERS
        $response = $this->get('/customer?country_id='.$customer->country_id);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        $response = $this->get('/customer?seller_id='.$customer->seller_id);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        $response = $this->get('/customer?status='.$customer->status);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        $response = $this->get('/customer?industry_id='.$customer->industry->id);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);
    }

    #[Test]
    public function it_filters_customers_by_industry_with_searchable_select(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/customer?industry_id='.$customer->industry_id);

        $response->assertOk();
        $response->assertSee($customer->name);
        $this->assertMatchesRegularExpression('/<select name="industry_id"[^>]*data-tom-select/', $response->getContent());
        $this->assertMatchesRegularExpression('/<option value="'.$customer->industry_id.'"\\s+selected="selected"\\s*>/', $response->getContent());
    }

    #[Test]
    public function the_autocomplete_searches_by_business_name_and_shows_it_in_the_label(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'name' => 'David',
            'business_name' => 'Zzrand',
        ]);
        Customer::factory()->create(['business_name' => 'Zzrand Other Company']);

        $response = $this->getJson('/customer?term=Zzrand', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertExactJson([['id' => $customer->id, 'label' => 'David Zzrand']]);
    }

    #[Test]
    public function the_autocomplete_matches_the_combined_name_and_business_name(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'name' => 'Zzdavid',
            'business_name' => 'Zzrand',
        ]);

        $response = $this->getJson('/customer?term=Zzdavid Zzrand', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertExactJson([['id' => $customer->id, 'label' => 'Zzdavid Zzrand']]);
    }

    #[Test]
    public function the_autocomplete_label_is_the_name_when_there_is_no_business_name(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'name' => 'Zzsolo',
            'business_name' => null,
        ]);

        $response = $this->getJson('/customer?term=Zzsolo', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertExactJson([['id' => $customer->id, 'label' => 'Zzsolo']]);
    }

    #[Test]
    public function the_customer_form_labels_business_name_as_last_name_too(): void
    {
        $response = $this->get('/customer/create');

        $response->assertOk();
        $response->assertSeeText(__('Business name').' / '.__('Last name'));
    }
}
