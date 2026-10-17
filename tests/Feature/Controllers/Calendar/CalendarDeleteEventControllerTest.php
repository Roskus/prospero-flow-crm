<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Calendar;

use App\Models\Calendar;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CalendarDeleteEventControllerTest extends TestCase
{
    #[Test]
    public function it_deletes_own_event_with_delete_method(): void
    {
        $event = Calendar::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->from('/calendar')->delete(route('calendar.delete', $event->id));

        $response->assertRedirect('/calendar');
        $this->assertNull(Calendar::find($event->id));
    }

    #[Test]
    public function it_does_not_delete_with_get_method(): void
    {
        $event = Calendar::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->get('/calendar/event/delete/'.$event->id);

        $response->assertMethodNotAllowed();
        $this->assertNotNull(Calendar::find($event->id));
    }

    #[Test]
    public function the_event_popup_delete_form_uses_delete_method(): void
    {
        $response = $this->get('/calendar');

        $response->assertOk();
        $response->assertSee('id="form_delete"', false);
        $response->assertSee('name="_method" value="DELETE"', false);
    }
}
