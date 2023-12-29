<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\MainController;
use App\Models\Notification;

class DeleteNotificationController extends MainController
{
    public function delete(int $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return redirect()->back();
    }
}
