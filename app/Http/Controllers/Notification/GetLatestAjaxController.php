<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\MainController;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetLatestAjaxController extends MainController
{
    public function getLatest(Request $request): JsonResponse
    {
        $notification = new Notification;

        return response()->json(['notifications' => $notification->getLatestByUser((int) Auth::user()->id)]);
    }
}
