<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Report;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;
use Tests\TestCase;

class ReportEmailControllerTest extends TestCase
{
    #[Test]
    public function each_sent_campaign_shows_its_figures_and_rates(): void
    {
        $campaign = $this->sentCampaign('Autumn newsletter');
        CampaignRecipient::factory()->count(4)->opened()->create(['campaign_id' => $campaign->id]);
        CampaignRecipient::factory()->count(4)->create(['campaign_id' => $campaign->id]);
        CampaignRecipient::factory()->count(2)->bounced()->create(['campaign_id' => $campaign->id]);

        $response = $this->get('/report/email')->assertOk()->assertSee('Autumn newsletter');

        $row = $response->viewData('rows')[0];
        $this->assertSame(10, $row['sent']);
        $this->assertSame(8, $row['delivered']);
        $this->assertSame(4, $row['opened']);
        $this->assertSame(2, $row['bounced']);
        $this->assertSame(50.0, $row['open_rate']);
        $this->assertSame(20.0, $row['bounce_rate']);
        $response->assertSee('50.0 %')->assertSee('20.0 %');
    }

    #[Test]
    public function a_recipient_opening_several_times_counts_once(): void
    {
        $campaign = $this->sentCampaign('Repeated');
        CampaignRecipient::factory()->opened(3)->create(['campaign_id' => $campaign->id]);
        CampaignRecipient::factory()->create(['campaign_id' => $campaign->id]);

        $row = $this->get('/report/email')->viewData('rows')[0];

        $this->assertSame(1, $row['opened']);
        $this->assertSame(50.0, $row['open_rate']);
    }

    #[Test]
    public function rates_are_zero_when_nothing_was_delivered(): void
    {
        $campaign = $this->sentCampaign('All bounced');
        CampaignRecipient::factory()->count(2)->bounced()->create(['campaign_id' => $campaign->id]);
        $this->sentCampaign('Empty');

        $rows = collect($this->get('/report/email')->viewData('rows'))->keyBy('campaign.name');

        $this->assertSame(0.0, $rows['All bounced']['open_rate']);
        $this->assertSame(100.0, $rows['All bounced']['bounce_rate']);
        $this->assertSame(0.0, $rows['Empty']['open_rate']);
        $this->assertSame(0.0, $rows['Empty']['bounce_rate']);
    }

    #[Test]
    public function campaigns_sent_before_tracking_show_their_emails_count(): void
    {
        $this->sentCampaign('Old campaign', ['emails_count' => 120]);

        $row = $this->get('/report/email')->viewData('rows')[0];

        $this->assertSame(120, $row['sent']);
        $this->assertSame(120, $row['delivered']);
        $this->assertSame(0, $row['opened']);
    }

    #[Test]
    public function totals_add_the_campaigns_and_compute_their_own_rates(): void
    {
        $first = $this->sentCampaign('First', ['send_at' => Carbon::parse('2026-09-01 10:00')]);
        CampaignRecipient::factory()->count(3)->opened()->create(['campaign_id' => $first->id]);
        CampaignRecipient::factory()->create(['campaign_id' => $first->id]);
        $second = $this->sentCampaign('Second', ['send_at' => Carbon::parse('2026-10-01 10:00')]);
        CampaignRecipient::factory()->count(4)->bounced()->create(['campaign_id' => $second->id]);
        CampaignRecipient::factory()->opened()->create(['campaign_id' => $second->id]);
        CampaignRecipient::factory()->create(['campaign_id' => $second->id]);

        $response = $this->get('/report/email');

        $this->assertSame(['Second', 'First'], collect($response->viewData('rows'))->pluck('campaign.name')->all());
        $this->assertSame(['sent' => 10, 'delivered' => 6, 'opened' => 4, 'bounced' => 4, 'open_rate' => 66.7, 'bounce_rate' => 40.0], $response->viewData('totals'));
    }

    #[Test]
    public function unsent_deleted_and_other_company_campaigns_are_not_listed(): void
    {
        Campaign::factory()->create(['company_id' => $this->user->company_id, 'name' => 'Draft campaign']);
        $this->sentCampaign('Deleted campaign')->delete();
        $other = Campaign::factory()->sent()->create(['name' => 'Foreign campaign']);
        CampaignRecipient::factory()->create(['campaign_id' => $other->id]);

        $response = $this->get('/report/email')
            ->assertOk()
            ->assertDontSee('Draft campaign')
            ->assertDontSee('Deleted campaign')
            ->assertDontSee('Foreign campaign')
            ->assertSee(__('No campaigns sent yet'));

        $this->assertSame(0, $response->viewData('totals')['sent']);
    }

    #[Test]
    public function the_report_requires_the_read_report_permission(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $employee = User::factory()->create(['company_id' => $this->user->company_id]);
        $employee->assignRole('User');

        $this->actingAs($employee)->get('/report/email')->assertForbidden();
    }

    #[Test]
    public function the_report_renders_in_spanish(): void
    {
        $this->user->update(['lang' => 'es']);
        $campaign = $this->sentCampaign('Boletín');
        CampaignRecipient::factory()->create(['campaign_id' => $campaign->id]);

        $this->get('/report/email')->assertOk()->assertSee('Tasa de apertura')->assertSee('Rebotados')->assertSee('Entregados');
    }

    #[Test]
    public function every_campaign_translation_key_exists_in_english_and_spanish(): void
    {
        $files = [
            ...File::allFiles(resource_path('views/campaign')),
            ...File::allFiles(app_path('Http/Controllers/Campaign')),
            resource_path('views/report/email.blade.php'),
            app_path('Http/Requests/CampaignSaveRequest.php'),
        ];

        $keys = collect($files)
            ->flatMap(function (SplFileInfo|string $file): array {
                preg_match_all('/__\(\s*([\'"])(.*?)(?<!\\\\)\1\s*[,)]/', File::get((string) $file), $matches);

                return $matches[2];
            })
            ->unique();

        foreach (['en', 'es'] as $locale) {
            $translations = json_decode(File::get(lang_path($locale.'.json')), true);

            $this->assertSame([], $keys->reject(fn (string $key): bool => array_key_exists($key, $translations))->values()->all(), "Missing {$locale} translations");
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function sentCampaign(string $name, array $attributes = []): Campaign
    {
        return Campaign::factory()->sent()->create(['company_id' => $this->user->company_id, 'name' => $name, ...$attributes]);
    }
}
