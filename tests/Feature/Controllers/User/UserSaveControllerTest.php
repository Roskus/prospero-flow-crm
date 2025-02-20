<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\User;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_a_user()
    {
        $response = $this->post('user/save', []);
        $response->assertSessionHasErrors(['first_name', 'email', 'lang']);

        $data = [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => 'en',
            'password' => 123,
            'password_confirmation' => 123,
            'company_id' => $this->user->company_id,
        ];
        $response = $this->post('user/save', $data);
        $response->assertRedirect('/user');

        $response = $this->get('/user');
        $response->assertSee($data['first_name']);
        $response->assertSee($data['last_name']);
        $response->assertSee($data['email']);
    }
}
