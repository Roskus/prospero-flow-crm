<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Models\CampaignRecipient;
use Illuminate\Http\Response;

/**
 * Public tracking image of campaign emails; the signed middleware rejects any URL that was not issued by the sender.
 */
class CampaignOpenController extends Controller
{
    private const string TRANSPARENT_GIF = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    public function open(int $recipient): Response
    {
        CampaignRecipient::whereKey($recipient)->whereNull('opened_at')->update(['opened_at' => now()]);
        CampaignRecipient::whereKey($recipient)->increment('open_count');

        return response(base64_decode(self::TRANSPARENT_GIF), 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
