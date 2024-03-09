<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\MainController;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationIndexController extends MainController
{
    public function index(Request $request)
    {
        $notification = new Notification();
        $data['notifications'] = $notification->getLatestByUser((int) Auth::user()->id, 20, true);

        return view('notification.index', $data);
    }
}
