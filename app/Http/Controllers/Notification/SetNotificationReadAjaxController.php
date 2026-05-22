<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\MainController;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetNotificationReadAjaxController extends MainController
{
    public function setRead(Request $request, int $id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $notification->fill(['read' => 1, 'updated_at' => now()]);
        $notification->save();

        return response()->json('');
    }
}
