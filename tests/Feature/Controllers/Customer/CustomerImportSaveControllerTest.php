<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerImportSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_import_customers_from_csv()
    {
        $response = $this->post('customer/import/save', []);
        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $fileName = 'pflow_customer_example_20230414.csv';
        $sourcePath = public_path('asset/upload/example/'.$fileName);
        $uploadDir = storage_path('uploads');

        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        copy($sourcePath, $uploadDir.'/'.$fileName);

        $file = new UploadedFile($sourcePath, $fileName, 'text/csv', null, true);
        $response = $this->post('customer/import/save', ['upload' => $file]);
        $response->assertRedirect('/customer');

        $customer = Customer::first();
        $this->assertEquals('11111122233', $customer->external_id);
        $this->assertEquals('John Doe', $customer->name);
        $this->assertEquals('John Doe Corp.', $customer->business_name);
        $this->assertEquals('1111111111', $customer->phone);
    }
}
