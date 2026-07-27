<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Rrhh\Employee;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmployeeSaveControllerTest extends TestCase
{
    #[Test]
    public function it_creates_employee_with_random_password_and_must_change_flag(): void
    {
        $response = $this->post('/rrhh/employee/save', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
        ]);

        $response->assertRedirect('/rrhh');
        $this->assertDatabaseHas('user', [
            'email' => 'jane.doe@example.com',
            'company_id' => $this->user->company_id,
            'is_employee' => true,
        ]);

        $user = User::where('email', 'jane.doe@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse(Hash::check('changeme', $user->password));
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertTrue($user->must_change_password);
        $this->assertTrue($user->hasRole('User'));
    }
}
