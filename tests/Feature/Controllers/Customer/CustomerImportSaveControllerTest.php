<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CustomerImportSaveControllerTest extends TestCase
{
    /** @test */
    public function it_can_import_customers_from_csv()
    {
        $response = $this->post('customer/import/save', []);
        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $path = str_replace('\\', DIRECTORY_SEPARATOR, public_path('asset\upload\example\pflow_customer_example_20221212.csv'));
        $file = new UploadedFile($path, 'pflow_customer_example_20221212.csv');
        $response = $this->post('customer/import/save', ['upload' => $file]);
        $response->assertRedirect('/customer');

        $customer = Customer::first();
        $this->assertEquals('John Doe Corp.', $customer->name);
        $this->assertEquals('John Doe Corp.', $customer->business_name);
        $this->assertEquals('1111111111', $customer->phone);
    }
}
