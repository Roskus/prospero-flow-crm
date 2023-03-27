<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->freezeTime();

        $role = Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->assignRole($role);

        $this->actingAs($this->user);
    }
}
