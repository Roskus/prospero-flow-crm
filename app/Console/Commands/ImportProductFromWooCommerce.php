<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Example: php artisan crm:import-product:woocommerce 4 www.medicagama.com.mx
 */
class ImportProductFromWooCommerce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:import-product:woocommerce
        {company_id : The ID of the Company}
        {domain : Domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from WooCommerce WHITOUT CREDENTIALS';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $return = Command::FAILURE;

        //$url = 'https://DOMAIN/wp-json/wc/store/v1/products';
        //'/wp-json/wc/store/v1/products'
        $woocommerce_product_api_path = '/wp-json/wc/store/products';
        $url = 'https://'.$this->argument('domain').$woocommerce_product_api_path;

        $company_id = $this->argument('company_id');

        $page = 1;
        do {
            $response = Http::accept('application/json')
                ->withoutVerifying()
                ->get($url, ['page' => $page]);

            if ($response->ok()) {
                $products = $response->collect();
                echo 'Importando '.$products->count().' productos...'.PHP_EOL;

                foreach ($products as $product) {
                    if (empty($product['sku'])) {
                        $product['sku'] = Str::limit($product['id'].'-'.str_slug($product['name']), 30, '');
                    }
                    $product['company_id'] = $company_id;
                    $category_name = $product['categories'][0]['name'];
                    if (! empty($category_name)) {
                        $category = Category::updateOrCreate([
                            'company_id' => $company_id,
                            'name' => $category_name,
                        ]);
                    }

                    $product['category_id'] = $category->id; //TODO woocommerce return an array of categories
                    $product['brand_id'] = 1; //TODO I think there is no similarity in woocommerce
                    $product['cost'] = (float) $product['prices']['price']; //TODO woocommerce return an object of price
                    $product['price'] = (float) $product['prices']['sale_price']; //TODO woocommerce return an object of
                    $photo = $product['images'][0]['src'];
                    if (! empty($photo)) {
                        $photo_url = explode('/', $photo);
                        $photo_path = public_path('/asset/upload/product');
                        shell_exec("wget $photo --directory-prefix=$photo_path");
                        echo $photo_url[count($photo_url) - 1];
                        $product['photo'] = $photo_url[count($photo_url) - 1];
                        //mkdir(public_path("/asset/upload/product/$product->id"))
                    }

                    Product::updateOrCreate([
                        'sku' => $product['sku'],
                    ], $product);
                }

                $return = Command::SUCCESS;
            }

            $page++;

            $response_next_page = Http::accept('application/json')
                ->withoutVerifying()
                ->get($url, ['page' => $page]);
        } while ($response_next_page->collect()->count() > 0);

        echo 'Proceso finalizado!';

        return $return;
    }
}
