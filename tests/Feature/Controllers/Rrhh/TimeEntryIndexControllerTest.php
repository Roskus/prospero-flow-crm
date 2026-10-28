<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Rrhh;

use App\Models\Company;
use App\Models\User;
use App\Models\WorkHour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class TimeEntryIndexControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function hr_sees_the_week_of_an_employee_of_the_company(): void
    {
        $employee = User::factory()->create(['company_id' => $this->user->company_id]);
        $monday = Carbon::now()->startOfWeek();
        WorkHour::create(['user_id' => $employee->id, 'start_time' => $monday->copy()->setTime(9, 0), 'end_time' => $monday->copy()->setTime(17, 0), 'type' => 'work']);

        $response = $this->get('/rrhh/time-entries?user_id='.$employee->id);

        $response->assertOk();
        $response->assertViewHas('employee_id', $employee->id);
        $response->assertViewHas('weekly_total', 7.0);
    }

    #[Test]
    public function an_employee_of_another_company_is_not_found(): void
    {
        $foreign = User::factory()->create(['company_id' => Company::factory()->create()->id]);

        $this->get('/rrhh/time-entries?user_id='.$foreign->id)->assertNotFound();
    }

    #[Test]
    public function an_employee_requesting_a_colleague_sees_their_own_entries(): void
    {
        $employee = User::factory()->create(['company_id' => $this->user->company_id]);
        $employee->givePermissionTo(Permission::findOrCreate('manage own timesheet', 'web'));
        $colleague = User::factory()->create(['company_id' => $this->user->company_id]);

        $this->actingAs($employee)->get('/rrhh/time-entries?user_id='.$colleague->id)
            ->assertOk()
            ->assertViewHas('employee_id', $employee->id);
    }
}
