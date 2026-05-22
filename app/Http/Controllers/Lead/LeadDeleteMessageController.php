<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead\Message as LeadMessage;
use Illuminate\Support\Facades\Auth;

class LeadDeleteMessageController extends MainController
{
    public function delete(int $id)
    {
        $message = LeadMessage::where('id', $id)
            ->where('author_id', Auth::id())
            ->firstOrFail();

        $message->delete();

        return back();
    }
}
