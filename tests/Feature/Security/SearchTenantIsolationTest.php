<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Customer;
use App\Models\Email;
use App\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchTenantIsolationTest extends TestCase
{
    #[Test]
    public function product_list_search_by_sku_does_not_leak_other_company_products(): void
    {
        $ownProduct = Product::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Own Widget', 'sku' => 'ZZSKU111']);
        $foreignProduct = Product::factory()->create(['name' => 'Acme Secret Widget', 'sku' => 'ZZSKU777']);

        $response = $this->get('/product?search=ZZSKU');

        $response->assertOk();
        $response->assertSee($ownProduct->name);
        $response->assertDontSee($foreignProduct->name);
    }

    #[Test]
    public function product_autocomplete_does_not_leak_other_company_products(): void
    {
        $ownProduct = Product::factory()->create(['company_id' => $this->user->company_id, 'sku' => 'ZZSKU111']);
        $foreignProduct = Product::factory()->create(['sku' => 'ZZSKU777']);

        $response = $this->getJson('/product?term=ZZSKU', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertJsonFragment(['id' => $ownProduct->id]);
        $response->assertJsonMissing(['id' => $foreignProduct->id]);
    }

    #[Test]
    public function email_search_does_not_leak_other_company_emails(): void
    {
        $ownEmail = Email::factory()->create(['company_id' => $this->user->company_id, 'subject' => 'ZZSUBJECT own']);
        $foreignEmail = Email::factory()->create(['subject' => 'ZZSUBJECT foreign']);

        $response = $this->get('/email?search=ZZSUBJECT');

        $response->assertOk();
        $response->assertSee($ownEmail->subject);
        $response->assertDontSee($foreignEmail->subject);
    }

    #[Test]
    public function customer_search_by_tag_does_not_leak_other_company_customers(): void
    {
        $ownCustomer = Customer::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Own Customer', 'tags' => ['zztag']]);
        $foreignCustomer = Customer::factory()->create(['name' => 'Foreign Customer', 'tags' => ['zztag']]);

        $response = $this->get('/customer?search=zztag');

        $response->assertOk();
        $response->assertSee($ownCustomer->name);
        $response->assertDontSee($foreignCustomer->name);
    }
}
