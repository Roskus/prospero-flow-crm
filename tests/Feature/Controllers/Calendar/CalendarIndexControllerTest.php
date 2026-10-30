<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Calendar;

use App\Models\Company;
use App\Models\CompanyHoliday;
use App\Models\TimeOff;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CalendarIndexControllerTest extends TestCase
{
    #[Test]
    public function it_shows_the_company_holidays_of_the_month(): void
    {
        foreach (['2026-12-24' => 'Nochebuena', '2026-12-25' => 'Navidad', '2026-12-31' => 'Fin de Año', '2027-01-01' => 'Año Nuevo'] as $date => $name) {
            CompanyHoliday::create(['company_id' => $this->user->company_id, 'date' => $date, 'name' => $name]);
        }
        CompanyHoliday::create(['company_id' => Company::factory()->create()->id, 'date' => '2026-12-08', 'name' => 'Other company holiday']);

        $response = $this->get(route('calendar.index', '2026-12-15'));

        $response->assertOk();
        $response->assertSeeInOrder(['Nochebuena', 'Navidad', 'Fin de Año', 'Año Nuevo']);
        $response->assertSee('bg-danger-subtle', false);
        $response->assertDontSee('Other company holiday');
    }

    #[Test]
    public function it_shows_approved_vacations_on_every_day_they_cover(): void
    {
        $employee = User::factory()->create(['company_id' => $this->user->company_id, 'first_name' => 'Zzana', 'last_name' => 'Vacaciones']);
        $this->createTimeOff($employee, 'vacation', 'approved', '2026-12-28', '2026-12-30');

        $response = $this->get(route('calendar.index', '2026-12-15'));

        $response->assertOk();
        $this->assertSame(3, substr_count($response->getContent(), 'Zzana Vacaciones'));
    }

    #[Test]
    public function it_shows_approved_personal_days_without_the_reason(): void
    {
        $employee = User::factory()->create(['company_id' => $this->user->company_id, 'first_name' => 'Zzpersonal', 'last_name' => 'Day']);
        $timeOff = $this->createTimeOff($employee, 'personal', 'approved', '2026-12-17', '2026-12-17');
        $timeOff->update(['reason' => 'Zzprivate reason']);

        $response = $this->get(route('calendar.index', '2026-12-15'));

        $response->assertOk();
        $response->assertSee('calendar-personal-day', false);
        $response->assertSee('Zzpersonal Day');
        $response->assertDontSee('Zzprivate reason');
    }

    #[Test]
    public function it_hides_pending_vacations_sick_leave_and_other_companies(): void
    {
        $employee = User::factory()->create(['company_id' => $this->user->company_id, 'first_name' => 'Zzpending', 'last_name' => 'Request']);
        $this->createTimeOff($employee, 'vacation', 'pending', '2026-12-14', '2026-12-14');

        $sick = User::factory()->create(['company_id' => $this->user->company_id, 'first_name' => 'Zzsick', 'last_name' => 'Leave']);
        $this->createTimeOff($sick, 'sick', 'approved', '2026-12-15', '2026-12-15');

        $foreign = User::factory()->create(['company_id' => Company::factory()->create()->id, 'first_name' => 'Zzforeign', 'last_name' => 'Employee']);
        $this->createTimeOff($foreign, 'vacation', 'approved', '2026-12-16', '2026-12-16');

        $response = $this->get(route('calendar.index', '2026-12-15'));

        $response->assertOk();
        $response->assertDontSee('Zzpending');
        $response->assertDontSee('Zzsick');
        $response->assertDontSee('Zzforeign');
    }

    #[Test]
    public function a_vacation_starting_in_the_previous_month_is_shown(): void
    {
        $employee = User::factory()->create(['company_id' => $this->user->company_id, 'first_name' => 'Zzlong', 'last_name' => 'Trip']);
        $this->createTimeOff($employee, 'vacation', 'approved', '2026-11-20', '2026-12-03');

        $this->get(route('calendar.index', '2026-12-15'))
            ->assertOk()
            ->assertSee('Zzlong Trip');
    }

    private function createTimeOff(User $user, string $type, string $status, string $start, string $end): TimeOff
    {
        return TimeOff::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'type' => $type,
            'start_date' => $start,
            'end_date' => $end,
            'days_used' => 1,
            'status' => $status,
        ]);
    }
}
