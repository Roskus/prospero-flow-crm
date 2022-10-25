<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Email;
use App\Models\EmailTemplate;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class LocalDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CompanyTableSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(IndustrySeeder::class);

        Account::factory(20)->create();
        Brand::factory(20)->create();
        Category::factory(20)->create();
        Industry::factory(20)->create();
        Lead::factory(20)->create();
        Customer::factory(20)->create();
        Contact::factory(20)->create();
        EmailTemplate::factory(20)->create();
        Email::factory(20)->create();
        Order::factory(20)->create();
        Product::factory(20)->create();
        Item::factory(20)->create();
        Supplier::factory(20)->create();
        Ticket::factory(20)->create();
    }
}
