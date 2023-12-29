<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetLatestAjaxController extends Controller
{
    public function getLatest(Request $request): \Illuminate\Http\JsonResponse
    {
        $notification = new Notification();

        return response()->json(['notifications' => $notification->getLatestByUser(Auth::user()->id)]);
    }
}
