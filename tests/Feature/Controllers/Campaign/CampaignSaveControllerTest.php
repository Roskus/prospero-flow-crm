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
        $campaign = Campaign::firstWhere('name', 'Autumn newsletter');
        $this->assertSame(['shoes', 'bags'], $campaign->tags);
        $this->assertSame(Campaign::AUDIENCE_TAGS, $campaign->audience);
        $this->assertSame(Campaign::RECIPIENTS_BOTH, $campaign->recipient_type);
    }

    #[Test]
    public function a_campaign_without_schedule_date_is_not_scheduled(): void
    {
        $this->post('/campaign/save', $this->payload(['schedule_send_date' => '', 'schedule_send_time' => '', 'audience' => Campaign::AUDIENCE_ALL, 'tags' => '']))->assertRedirect('/campaign');

        $this->assertNull(Campaign::firstWhere('name', 'Autumn newsletter')->schedule_at);
    }

    #[Test]
    public function sending_by_tags_requires_a_tag(): void
    {
        $this->post('/campaign/save', $this->payload(['audience' => Campaign::AUDIENCE_TAGS, 'tags' => '']))
            ->assertSessionHasErrors('tags');

        $this->post('/campaign/save', $this->payload(['audience' => 'everyone']))
            ->assertSessionHasErrors('audience');

        $this->post('/campaign/save', $this->payload(['recipient_type' => 'suppliers']))
            ->assertSessionHasErrors('recipient_type');

        $this->assertDatabaseCount('campaign', 0);
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
    public function the_list_shows_the_status_badge_and_the_recipients(): void
    {
        Campaign::factory()->sent()->create(['company_id' => $this->user->company_id, 'recipient_type' => Campaign::RECIPIENTS_CUSTOMERS]);
        Campaign::factory()->create(['company_id' => $this->user->company_id, 'recipient_type' => Campaign::RECIPIENTS_BOTH, 'audience' => Campaign::AUDIENCE_TAGS, 'tags' => ['vip']]);
        Campaign::factory()->create(['company_id' => $this->user->company_id, 'schedule_send_date' => null, 'schedule_at' => null]);

        $this->get('/campaign')
            ->assertOk()
            ->assertSee('<span class="badge text-bg-success">'.__('Sent').'</span>', false)
            ->assertSee('<span class="badge text-bg-primary">'.__('Scheduled').'</span>', false)
            ->assertSee('<span class="badge text-bg-warning">'.__('Draft').'</span>', false)
            ->assertSee(__('Recipients'))
            ->assertSee(__('Customers'))
            ->assertSee(__('Leads and customers'))
            ->assertSee(__('Only those with any of these tags'));
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
            'recipient_type' => Campaign::RECIPIENTS_BOTH,
            'audience' => Campaign::AUDIENCE_TAGS,
            'tags' => 'shoes, bags',
            'schedule_send_date' => now()->addDay()->toDateString(),
            'schedule_send_time' => '10:30',
            ...$overrides,
        ];
    }
}
