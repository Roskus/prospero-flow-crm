<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Email;
use App\Models\Industry;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Product;
use App\Models\Ticket;
use App\Models\Ticket\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevelopmentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);

        $company = Company::factory()->create();

        $user = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'email' => 'admin@admin.com',
            'password' => '$2y$10$Rbren9IPDJs8/nbZQ5.z8.5wW.LmukvaLyL9ndnqZ3NH.AbdrPJLK', //admin
            'lang' => 'en',
            'company_id' => $company->id,
        ]);
        $user->assignRole('SuperAdmin');

        $industries = Industry::factory()->count(10)->create();
        $categories = Category::factory()->count(10)->create(['company_id' => $company->id]);
        $brands = Brand::factory()->count(10)->create(['company_id' => $company->id]);

        Lead::factory()->count(10)->recycle($industries)->create([
            'company_id' => $company->id,
            'seller_id' => $user->id,
        ]);

        $customers = Customer::factory()->count(10)->recycle($industries)->create([
            'company_id' => $company->id,
            'seller_id' => $user->id,
        ]);

        $orders = Order::factory()->count(10)->recycle($customers)->create([
            'company_id' => $company->id,
            'seller_id' => $user->id,
        ]);

        $products = Product::factory()->count(10)->recycle($categories)->recycle($brands)->create([
            'company_id' => $company->id,
            'photo' => null,
        ]);

        Item::factory()->count(30)->recycle($products)->recycle($orders)->create();

        Email::factory()->count(10)->create();

        $tickets = Ticket::factory()->count(10)->create();

        Message::factory()->count(10)->recycle($tickets)->create();
    }
}
