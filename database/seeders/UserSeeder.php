<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = Role::findByName('SuperAdmin');
        $user = User::factory()->create([
            'company_id' => 1,
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'lang' => 'en',
        ]);
        $user->assignRole($superAdmin);
    }
}
