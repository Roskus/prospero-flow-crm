<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('user')->count() === 0) {
            $superAdmin = Role::findByName('SuperAdmin');
            $user = User::factory()->create([
                'company_id' => Company::DEFAULT_COMPANY,
                'first_name' => 'Admin',
                'last_name' => 'Test',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin'),
                'lang' => 'en',
            ]);
            $user->assignRole($superAdmin);
        }
    }
}
