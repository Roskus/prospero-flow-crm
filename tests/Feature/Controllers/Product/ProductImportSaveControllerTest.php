<?php

namespace Tests\Feature\Controllers\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductImportSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_import_products_from_csv()
    {
        Category::create(['name' => 'category test', 'company_id' => $this->user->company_id]);
        Brand::create(['name' => 'DELL', 'company_id' => $this->user->company_id]);

        $response = $this->post('product/import/save', []);
        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $path = str_replace('\\', DIRECTORY_SEPARATOR, base_path('tests\Feature\Controllers\Product\hammer_product_example_20221206.csv'));
        $file = new UploadedFile($path, 'prospero_product_example_20221206.csv');
        $response = $this->post('product/import/save', ['upload' => $file]);
        $response->assertRedirect('/product');

        $product = Product::first();
        $this->assertEquals('Test product XPS13', $product->name);
        $this->assertEquals('category test', $product->category->name);
        $this->assertEquals('DELL', $product->brand->name);
    }
}
