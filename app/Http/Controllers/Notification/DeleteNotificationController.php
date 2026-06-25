<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\MainController;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class DeleteNotificationController extends MainController
{
    public function delete(int $id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $notification->delete();

        return redirect()->back();
    }
}
