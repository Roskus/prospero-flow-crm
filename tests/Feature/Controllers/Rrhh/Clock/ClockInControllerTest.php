<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Rrhh\Clock;

use App\Models\WorkHour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClockInControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function clocking_in_opens_an_entry_without_end_time(): void
    {
        $this->post('/rrhh/clock/in')->assertSessionHas('status', 'success');

        $entry = WorkHour::sole();
        $this->assertSame($this->user->id, $entry->user_id);
        $this->assertNull($entry->end_time);
    }

    #[Test]
    public function clocking_out_closes_the_open_entry(): void
    {
        $this->post('/rrhh/clock/in');
        $this->travel(2)->hours();

        $this->post('/rrhh/clock/out');

        $this->assertNotNull(WorkHour::sole()->end_time);
    }
}
