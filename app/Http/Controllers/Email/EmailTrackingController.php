<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\Email;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class EmailTrackingController extends Controller
{
    public function trackEmail(string $uuid): Response
    {
        $email = Email::where('uuid', $uuid)->first();

        if ($email && $email->opened_at === null) {
            $email->opened_at = Carbon::now();
            $email->save();
        }

        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Content-Length' => (string) strlen($pixel),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
