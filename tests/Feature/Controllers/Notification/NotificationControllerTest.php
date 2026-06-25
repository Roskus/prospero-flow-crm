<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Notification;

use App\Models\Notification;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_get_latest(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get('/ajax/notification');

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_get_latest_notifications(): void
    {
        Notification::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get('/ajax/notification');

        $response->assertOk();
        $response->assertJsonStructure(['notifications']);
    }

    #[Test]
    public function it_blocks_unauthenticated_set_read(): void
    {
        $notification = Notification::factory()->create(['user_id' => $this->user->id]);

        auth()->guard('web')->logout();

        $response = $this->get("/notification/read/{$notification->id}");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_mark_own_notification_as_read(): void
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'read' => 0,
        ]);

        $response = $this->get("/notification/read/{$notification->id}");

        $response->assertOk();
        $this->assertDatabaseHas('notification', ['id' => $notification->id, 'read' => 1]);
    }

    #[Test]
    public function it_blocks_marking_another_users_notification_as_read(): void
    {
        $otherUser = User::factory()->create();
        $notification = Notification::factory()->create([
            'user_id' => $otherUser->id,
            'read' => 0,
        ]);

        $response = $this->get("/notification/read/{$notification->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_blocks_unauthenticated_delete(): void
    {
        $notification = Notification::factory()->create(['user_id' => $this->user->id]);

        auth()->guard('web')->logout();

        $response = $this->get("/notification/delete/{$notification->id}");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_delete_own_notification(): void
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get("/notification/delete/{$notification->id}");

        $response->assertRedirect();
        $this->assertTrue(Notification::where('id', $notification->id)->onlyTrashed()->exists());
    }

    #[Test]
    public function it_prevents_deleting_another_users_notification(): void
    {
        $otherUser = User::factory()->create();
        $notification = Notification::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->get("/notification/delete/{$notification->id}");

        $response->assertNotFound();
        $this->assertNull(Notification::withTrashed()->find($notification->id)->deleted_at);
    }
}
