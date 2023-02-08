<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\Ticket\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (User::all() as $user) {
            Ticket::factory()->has(Message::factory()->count(5))->count(15)->create([
                'created_by' => $user,
                'company_id' => $user->company,
            ]);
        }
    }
}
