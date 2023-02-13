<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Email;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Order\Item;
use App\Models\Ticket;
use App\Models\Ticket\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevelopmentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Company::factory()
            ->has(User::factory()->state([
                'first_name' => 'Admin',
                'last_name' => 'Test',
                'email' => 'admin@admin.com',
                'password' => '$2y$10$Rbren9IPDJs8/nbZQ5.z8.5wW.LmukvaLyL9ndnqZ3NH.AbdrPJLK', //admin
                'lang' => 'en', ]
            ))
            ->has(Lead::factory()->count(10)->state(['seller_id' => 1]))
            ->has(
                Customer::factory()
                    ->has(
                        Order::factory()
                            ->has(
                                Item::factory()->count(5))->count(5)->state(['company_id' => 1]))->count(10)->state(['seller_id' => 1]))
            ->has(Email::factory()->count(10))
            ->has(Ticket::factory()->has(Message::factory()->count(5)->state(['author_id' => 1]))->count(10)->state(['customer_id' => 1, 'created_by' => 1, 'assigned_to' => 1]))
            ->create();
    }
}
