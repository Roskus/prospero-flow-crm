<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\GenericEmail;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Scopes\AssignedSellerScope;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Symfony\Component\Mailer\Exception\UnexpectedResponseException;

class CampaignSender extends Command
{
    private const int LEADS_PER_CHUNK = 200;

    private const int PERMANENT_FAILURE_CODE = 500;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:campaign:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the email campaigns whose scheduled date and time have been reached';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $campaigns = Campaign::query()
            ->whereNull('send_at')
            ->whereNotNull('schedule_at')
            ->where('schedule_at', '<=', now())
            ->get();

        $result = Command::SUCCESS;
        foreach ($campaigns as $campaign) {
            if (! $this->send($campaign)) {
                $result = Command::FAILURE;
            }
        }

        return $result;
    }

    /**
     * Each recipient row is written before its email is sent, so a run that stops half way never mails anyone twice.
     * When the mail server cannot take emails the run stops and the campaign stays pending, to be resumed by the next run.
     */
    private function send(Campaign $campaign): bool
    {
        $this->info("Campaign to send: $campaign->name");
        $company = Company::find($campaign->company_id);
        if ($company === null) {
            return false;
        }
        $sender = User::where('company_id', $campaign->company_id)->where('email', $campaign->from)->first();

        try {
            Lead::query()
                ->withoutGlobalScope(AssignedSellerScope::class)
                ->where('company_id', $campaign->company_id)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->where('opt_in', true)
                ->chunkById(self::LEADS_PER_CHUNK, function (Collection $leads) use ($campaign, $company, $sender): void {
                    $alreadySent = $campaign->recipients()->whereIn('email', $leads->pluck('email'))->pluck('email')->all();

                    foreach ($leads as $lead) {
                        if (in_array($lead->email, $alreadySent, true)) {
                            continue;
                        }
                        $alreadySent[] = $lead->email;
                        $this->sendTo($campaign, $company, $sender, $lead);
                    }
                });
        } catch (\Throwable $t) {
            Log::error("Campaign $campaign->id stopped: {$t->getMessage()}");
            $this->error("Campaign stopped, it will be resumed on the next run: {$t->getMessage()}");
            $this->summary($campaign);

            return false;
        }

        $campaign->send_at = now();
        $campaign->emails_count = $campaign->recipients()->count();
        $campaign->save();
        $this->summary($campaign);

        return true;
    }

    /**
     * A permanent rejection of the recipient (5xx) is a bounce; any other failure means the mail server cannot take emails now.
     */
    private function sendTo(Campaign $campaign, Company $company, ?User $sender, Lead $lead): void
    {
        $recipient = $campaign->recipients()->create([
            'lead_id' => $lead->id,
            'email' => $lead->email,
            'status' => CampaignRecipient::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $email = new GenericEmail($company, $sender, Campaign::personalize($campaign->subject, $lead, isHtml: false), [
            'body' => Campaign::personalize($campaign->body, $lead),
            'tracking_url' => URL::signedRoute('campaign.open', ['recipient' => $recipient->id]),
        ]);

        try {
            Mail::to($lead->email)->send($email);
            $this->line("Sent: $lead->email");
        } catch (UnexpectedResponseException $e) {
            if ($e->getCode() < self::PERMANENT_FAILURE_CODE) {
                $recipient->delete();

                throw $e;
            }
            Log::warning("Campaign $campaign->id bounced $lead->email: {$e->getMessage()}");
            $recipient->update(['status' => CampaignRecipient::STATUS_BOUNCED, 'error' => mb_substr($e->getMessage(), 0, 1000)]);
            $this->warn("Bounced: $lead->email");
        } catch (\Throwable $t) {
            $recipient->delete();

            throw $t;
        }
    }

    private function summary(Campaign $campaign): void
    {
        $counts = $campaign->recipients()->toBase()->selectRaw('status, COUNT(*) AS total')->groupBy('status')->pluck('total', 'status');

        $this->info(sprintf(
            'Sent: %d, bounced: %d',
            $counts->get(CampaignRecipient::STATUS_SENT, 0),
            $counts->get(CampaignRecipient::STATUS_BOUNCED, 0),
        ));
    }
}
