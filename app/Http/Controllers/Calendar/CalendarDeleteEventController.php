<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use App\Models\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarDeleteEventController extends MainController
{
    public function delete(Request $request, int $id)
    {
        Calendar::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail()
            ->delete();

        return back();
    }
}
