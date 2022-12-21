<?php

namespace Tests\Feature\Controllers\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductImportSaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_import_products_from_csv()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        Category::create(['name' => 'category test', 'company_id' => 1]);
        Brand::create(['name' => 'DELL', 'company_id' => 1]);

        $response = $this->post('product/import/save', []);
        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $path = str_replace('\\', DIRECTORY_SEPARATOR, base_path('tests\Feature\Controllers\Product\hammer_product_example_20221206.csv'));
        $file = new UploadedFile($path, 'hammer_product_example_20221206.csv');
        $response = $this->post('product/import/save', ['upload' => $file]);
        $response->assertRedirect('/product');

        $product = Product::first();
        $this->assertEquals('Test product XPS13', $product->name);
        $this->assertEquals('category test', $product->category->name);
        $this->assertEquals('DELL', $product->brand->name);
    }
}
