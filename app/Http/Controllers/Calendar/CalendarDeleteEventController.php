<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use App\Models\Calendar;
use Illuminate\Http\Request;

class CalendarDeleteEventController extends MainController
{
    public function delete(Request $request, int $id)
    {
        Calendar::find($id)->delete();

        return back();
    }
}
