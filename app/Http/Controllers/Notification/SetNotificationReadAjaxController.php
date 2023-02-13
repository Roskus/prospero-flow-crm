<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class setNotificationReadAjaxController extends Controller
{
    public function setRead(Request $request, int $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->fill(['read' => 1, 'updated_at' => now()]);
        $notification->save();

        return response()->json('');
    }
}
