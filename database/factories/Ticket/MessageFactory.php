<?php

declare(strict_types=1);

namespace Database\Factories\Ticket;

use App\Models\Ticket;
use App\Models\Ticket\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'body' => $this->faker->text(),
            'author_id' => User::factory(),
            'ticket_id' => Ticket::factory(),
        ];
    }
}
