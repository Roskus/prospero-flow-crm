<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Calendar;

use App\Models\Calendar;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CalendarSaveEventControllerTest extends TestCase
{
    #[Test]
    public function it_saves_a_valid_event(): void
    {
        $response = $this->from('/calendar')->post(route('calendar.save'), [
            'title' => 'Team meeting',
            'date' => '2026-10-20',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'description' => 'Weekly sync',
            'guest_list' => ['guest@example.com'],
            'meeting' => 'https://meet.example.com/abc',
            'address' => 'Main street 1',
        ]);

        $response->assertRedirect('/calendar');
        $response->assertSessionHasNoErrors();

        $event = Calendar::where('title', 'Team meeting')->firstOrFail();
        $this->assertSame($this->user->id, $event->user_id);
        $this->assertSame($this->user->company_id, $event->company_id);
        $this->assertSame(['guest@example.com'], $event->guests);
        $this->assertStringStartsWith('2026-10-20 09:00', (string) $event->start_date);
        $this->assertStringStartsWith('2026-10-20 10:00', (string) $event->end_date);
    }

    #[Test]
    public function it_saves_an_all_day_event_without_times(): void
    {
        $response = $this->from('/')->post(route('calendar.save'), [
            'title' => 'Holiday',
            'date' => '2026-10-20',
            'is_all_day' => '1',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasNoErrors();

        $event = Calendar::where('title', 'Holiday')->firstOrFail();
        $this->assertTrue((bool) $event->is_all_day);
        $this->assertStringStartsWith('2026-10-20 00:00', (string) $event->start_date);
        $this->assertStringStartsWith('2026-10-20 23:59', (string) $event->end_date);
    }

    #[Test]
    public function it_requires_title_date_and_times(): void
    {
        $response = $this->from('/')->post(route('calendar.save'), []);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['title', 'date', 'start_time', 'end_time']);
        $this->assertDatabaseCount('calendar', 0);
    }

    #[Test]
    public function it_rejects_an_end_time_before_the_start_time(): void
    {
        $response = $this->from('/')->post(route('calendar.save'), [
            'title' => 'Wrong times',
            'date' => '2026-10-20',
            'start_time' => '11:00',
            'end_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('end_time');
        $this->assertDatabaseMissing('calendar', ['title' => 'Wrong times']);
    }

    #[Test]
    public function it_rejects_invalid_formats(): void
    {
        $response = $this->from('/')->post(route('calendar.save'), [
            'title' => str_repeat('a', 256),
            'date' => 'not-a-date',
            'start_time' => '9am',
            'end_time' => '25:00',
            'latitude' => 'north',
            'guest_list' => 'guest@example.com',
        ]);

        $response->assertSessionHasErrors(['title', 'date', 'start_time', 'end_time', 'latitude', 'guest_list']);
    }

    #[Test]
    public function it_ignores_fields_that_are_not_validated(): void
    {
        $otherUser = User::factory()->create();

        $this->from('/')->post(route('calendar.save'), [
            'title' => 'Injected',
            'date' => '2026-10-20',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'user_id' => $otherUser->id,
            'company_id' => $otherUser->company_id,
        ]);

        $this->assertDatabaseHas('calendar', [
            'title' => 'Injected',
            'user_id' => $this->user->id,
            'company_id' => $this->user->company_id,
        ]);
    }

    #[Test]
    public function validation_errors_are_shown_on_the_dashboard(): void
    {
        $this->from('/')->post(route('calendar.save'), ['date' => '2026-10-20', 'start_time' => '09:00', 'end_time' => '10:00']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('alert alert-danger', false);
    }

    #[Test]
    public function it_forbids_users_without_permission(): void
    {
        $user = User::factory()->create();
        $user->assignRole('User');
        $user->givePermissionTo(Permission::create(['name' => 'read calendar', 'guard_name' => 'web']));

        $response = $this->actingAs($user)->post(route('calendar.save'), [
            'title' => 'Forbidden',
            'date' => '2026-10-20',
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('calendar', ['title' => 'Forbidden']);
    }

    #[Test]
    public function it_saves_and_replaces_the_guest_list(): void
    {
        $event = Calendar::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
            'guests' => ['old@example.com'],
        ]);

        $this->from('/calendar')->post(route('calendar.save'), [
            'id' => $event->id,
            'title' => 'With guests',
            'date' => '2026-10-20',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'guest_list' => ['ana@example.com', 'luis@example.com'],
        ]);

        $this->assertSame(['ana@example.com', 'luis@example.com'], $event->fresh()->guests);

        $this->from('/calendar')->post(route('calendar.save'), [
            'id' => $event->id,
            'title' => 'Without guests',
            'date' => '2026-10-20',
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $this->assertNull($event->fresh()->guests);
    }

    #[Test]
    public function the_event_popup_sends_guests_as_hidden_inputs(): void
    {
        $response = $this->get('/calendar');

        $response->assertOk();
        $response->assertSee('<ul id="guest_list"', false);
        $response->assertDontSee('<select name="guest_list[]"', false);
        $response->assertDontSee('name="guest"', false);
    }
}
