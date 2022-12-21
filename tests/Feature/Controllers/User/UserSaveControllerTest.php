<?php

namespace Tests\Feature\Controllers\User;

use Tests\TestCase;

class UserSaveControllerTest extends TestCase
{
    /** @test */
    public function it_can_save_a_user()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        $response = $this->post('user/save', []);
        $response->assertSessionHasErrors(['first_name', 'email', 'lang']);

        $data = [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => fake()->randomElement(array_keys(config('app.locales'))),
            'password' => 123,
            'password_confirmation' => 123,
        ];
        $response = $this->post('user/save', $data);
        $response->assertRedirect('/user');

        $response = $this->get('/user');
        $response->assertSee($data['first_name']);
        $response->assertSee($data['last_name']);
        $response->assertSee($data['email']);
    }
}
