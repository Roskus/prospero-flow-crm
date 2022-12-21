<?php

namespace Tests;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function signIn()
    {
        Company::find(1) ?? Company::factory()->create(['id' => 1]);
        $user = User::find(1) ?? User::factory()->create(['id' => 1]);

        $this->actingAs($user);

        return $user;
    }
}
