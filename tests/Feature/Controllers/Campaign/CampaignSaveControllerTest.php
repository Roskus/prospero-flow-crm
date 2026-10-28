<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Campaign;

use App\Models\Campaign;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CampaignSaveControllerTest extends TestCase
{
    #[Test]
    public function a_campaign_is_saved_with_its_name(): void
    {
        $this->post('/campaign/save', $this->payload())
            ->assertRedirect('/campaign')
            ->assertSessionHas('status', 'success');

        $this->assertDatabaseHas('campaign', [
            'company_id' => $this->user->company_id,
            'name' => 'Autumn newsletter',
            'subject' => 'New products',
            'from' => $this->user->email,
        ]);
        $this->assertSame(['shoes', 'bags'], Campaign::firstWhere('name', 'Autumn newsletter')->tags);
    }

    #[Test]
    public function a_campaign_without_schedule_date_is_not_scheduled(): void
    {
        $this->post('/campaign/save', $this->payload(['schedule_send_date' => '', 'schedule_send_time' => '']))->assertRedirect('/campaign');

        $this->assertNull(Campaign::firstWhere('name', 'Autumn newsletter')->schedule_at);
    }

    #[Test]
    public function the_name_is_required(): void
    {
        $this->post('/campaign/save', $this->payload(['name' => '']))
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('campaign', 0);
    }

    #[Test]
    public function the_from_address_must_be_the_company_or_the_user(): void
    {
        $this->post('/campaign/save', $this->payload(['from' => 'someone@else.com']))
            ->assertSessionHasErrors('from');

        $this->assertDatabaseCount('campaign', 0);
    }

    #[Test]
    public function a_campaign_of_another_company_cannot_be_saved(): void
    {
        $campaign = Campaign::factory()->create(['name' => 'Theirs']);

        $this->post('/campaign/save', $this->payload(['id' => $campaign->id]))->assertNotFound();
        $this->get("/campaign/update/$campaign->id")->assertNotFound();

        $this->assertSame('Theirs', $campaign->fresh()->name);
    }

    #[Test]
    public function the_list_shows_the_name_and_the_form_the_name_field(): void
    {
        $campaign = Campaign::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Spring launch']);

        $this->get('/campaign')->assertOk()->assertSee('Spring launch')->assertDontSee('Campaign.send');
        $this->get("/campaign/update/$campaign->id")->assertOk()->assertSee('name="name"', false)->assertSee('Spring launch');
    }

    #[Test]
    public function a_campaign_is_deleted(): void
    {
        $campaign = Campaign::factory()->create(['company_id' => $this->user->company_id]);

        $this->delete("/campaign/$campaign->id")
            ->assertRedirect('campaign')
            ->assertSessionHas('status', 'success');

        $this->assertSoftDeleted('campaign', ['id' => $campaign->id]);
    }

    #[Test]
    public function a_campaign_of_another_company_cannot_be_deleted(): void
    {
        $campaign = Campaign::factory()->create();

        $this->delete("/campaign/$campaign->id")->assertNotFound();

        $this->assertNotSoftDeleted('campaign', ['id' => $campaign->id]);
    }

    #[Test]
    public function deleting_requires_the_delete_permission(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $support = User::factory()->create(['company_id' => $this->user->company_id]);
        $support->assignRole('Support');
        $campaign = Campaign::factory()->create(['company_id' => $this->user->company_id]);

        $this->actingAs($support)->delete("/campaign/$campaign->id")->assertForbidden();

        $this->assertNotSoftDeleted('campaign', ['id' => $campaign->id]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Autumn newsletter',
            'subject' => 'New products',
            'from' => $this->user->email,
            'body' => '<p>Hello</p>',
            'tags' => 'shoes, bags',
            'schedule_send_date' => now()->addDay()->toDateString(),
            'schedule_send_time' => '10:30',
            ...$overrides,
        ];
    }
}
