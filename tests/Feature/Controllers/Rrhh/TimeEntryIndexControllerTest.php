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
    public function updating_an_entry_returns_to_the_week_of_the_entry(): void
    {
        $date = Carbon::now()->startOfWeek()->subWeeks(3)->addDays(2);
        $entry = WorkHour::create(['user_id' => $this->user->id, 'start_time' => $date->copy()->setTime(9, 0), 'end_time' => $date->copy()->setTime(17, 0), 'type' => 'work']);

        $this->post('/rrhh/time-entries/update/'.$entry->id, [
            'entry_date' => $date->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
        ])->assertRedirect('/rrhh/time-entries?week=-3');

        $this->assertSame('08:00', $entry->fresh()->start_time->format('H:i'));
    }

    #[Test]
    public function updating_an_entry_to_another_week_returns_to_the_new_week(): void
    {
        $date = Carbon::now()->startOfWeek()->subWeeks(3);
        $entry = WorkHour::create(['user_id' => $this->user->id, 'start_time' => $date->copy()->setTime(9, 0), 'end_time' => $date->copy()->setTime(17, 0), 'type' => 'work']);

        $this->post('/rrhh/time-entries/update/'.$entry->id, [
            'entry_date' => $date->copy()->addWeek()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ])->assertRedirect('/rrhh/time-entries?week=-2');
    }

    #[Test]
    public function saving_an_entry_returns_to_the_week_of_the_entry(): void
    {
        $date = Carbon::now()->startOfWeek()->subWeeks(3)->addDays(4);

        $this->post('/rrhh/time-entries/save', [
            'user_id' => $this->user->id,
            'entry_date' => $date->toDateString(),
            'start_time' => '09:00',
            'end_time' => '13:00',
        ])->assertRedirect('/rrhh/time-entries?user_id='.$this->user->id.'&week=-3');
    }

    #[Test]
    public function saving_an_entry_of_the_current_week_returns_to_week_zero(): void
    {
        $this->post('/rrhh/time-entries/save', [
            'user_id' => $this->user->id,
            'entry_date' => Carbon::now()->endOfWeek()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '13:00',
        ])->assertRedirect('/rrhh/time-entries?user_id='.$this->user->id.'&week=0');
    }

    #[Test]
    public function an_entry_starting_at_the_same_time_on_the_same_day_is_rejected(): void
    {
        $date = Carbon::now()->startOfWeek()->addDays(3);
        $this->createEntry($this->user, $date, '08:00', '17:00');

        $this->post('/rrhh/time-entries/save', [
            'user_id' => $this->user->id,
            'entry_date' => $date->toDateString(),
            'start_time' => '08:00',
            'end_time' => '13:00',
        ])->assertRedirect('/rrhh/time-entries?user_id='.$this->user->id.'&week=0')
            ->assertSessionHas('status', 'error')
            ->assertSessionHas('message', __('The time entry overlaps another one of the same person (:date :start — :end).', [
                'date' => $date->format('d/m/Y'), 'start' => '08:00', 'end' => '17:00',
            ]));

        $this->assertSame(1, WorkHour::where('user_id', $this->user->id)->count());
    }

    #[Test]
    public function an_entry_inside_another_one_is_rejected(): void
    {
        $date = Carbon::now()->startOfWeek();
        $this->createEntry($this->user, $date, '08:00', '17:00');

        $this->post('/rrhh/time-entries/save', [
            'user_id' => $this->user->id,
            'entry_date' => $date->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
        ])->assertSessionHas('status', 'error');

        $this->assertSame(1, WorkHour::where('user_id', $this->user->id)->count());
    }

    #[Test]
    public function an_entry_overlapping_an_open_entry_is_rejected(): void
    {
        $date = Carbon::now()->startOfWeek();
        WorkHour::create(['user_id' => $this->user->id, 'start_time' => $date->copy()->setTime(9, 0), 'end_time' => null, 'type' => 'work']);

        $this->post('/rrhh/time-entries/save', [
            'user_id' => $this->user->id,
            'entry_date' => $date->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
        ])->assertSessionHas('status', 'error');
    }

    #[Test]
    public function a_split_shift_is_allowed(): void
    {
        $date = Carbon::now()->startOfWeek();
        $this->createEntry($this->user, $date, '08:00', '13:00');

        $this->post('/rrhh/time-entries/save', [
            'user_id' => $this->user->id,
            'entry_date' => $date->toDateString(),
            'start_time' => '13:00',
            'end_time' => '17:00',
        ])->assertSessionHas('status', 'success');

        $this->assertSame(2, WorkHour::where('user_id', $this->user->id)->count());
    }

    #[Test]
    public function another_person_can_have_an_entry_at_the_same_time(): void
    {
        $date = Carbon::now()->startOfWeek();
        $colleague = User::factory()->create(['company_id' => $this->user->company_id]);
        $this->createEntry($colleague, $date, '08:00', '17:00');

        $this->post('/rrhh/time-entries/save', [
            'user_id' => $this->user->id,
            'entry_date' => $date->toDateString(),
            'start_time' => '08:00',
            'end_time' => '17:00',
        ])->assertSessionHas('status', 'success');
    }

    #[Test]
    public function updating_an_entry_onto_another_one_is_rejected(): void
    {
        $date = Carbon::now()->startOfWeek();
        $this->createEntry($this->user, $date, '08:00', '13:00');
        $afternoon = $this->createEntry($this->user, $date, '15:00', '19:00');

        $this->post('/rrhh/time-entries/update/'.$afternoon->id, [
            'entry_date' => $date->toDateString(),
            'start_time' => '12:00',
            'end_time' => '19:00',
        ])->assertSessionHas('status', 'error');

        $this->assertSame('15:00', $afternoon->fresh()->start_time->format('H:i'));
    }

    #[Test]
    public function updating_an_entry_without_moving_it_does_not_overlap_itself(): void
    {
        $date = Carbon::now()->startOfWeek();
        $entry = $this->createEntry($this->user, $date, '08:00', '17:00');

        $this->post('/rrhh/time-entries/update/'.$entry->id, [
            'entry_date' => $date->toDateString(),
            'start_time' => '08:00',
            'end_time' => '16:00',
            'notes' => 'Left early',
        ])->assertSessionHas('status', 'success');

        $this->assertSame('16:00', $entry->fresh()->end_time->format('H:i'));
    }

    #[Test]
    public function validation_errors_are_shown_on_the_page(): void
    {
        $this->from('/rrhh/time-entries')->post('/rrhh/time-entries/save', [
            'user_id' => $this->user->id,
            'entry_date' => Carbon::now()->toDateString(),
            'start_time' => '17:00',
            'end_time' => '08:00',
        ])->assertSessionHasErrors('end_time');

        $this->get('/rrhh/time-entries')->assertSee('alert alert-danger', false);
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

    private function createEntry(User $user, Carbon $date, string $start, string $end): WorkHour
    {
        return WorkHour::create([
            'user_id' => $user->id,
            'start_time' => $date->copy()->setTimeFromTimeString($start),
            'end_time' => $date->copy()->setTimeFromTimeString($end),
            'type' => 'work',
        ]);
    }
}
