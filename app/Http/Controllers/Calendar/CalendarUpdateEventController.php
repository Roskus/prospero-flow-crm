<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarUpdateEventController extends MainController
{
    public function update(Request $request, int $id): JsonResponse
    {
        $calendar = Calendar::find($id);

        return response()->json(['calendar' => $calendar]);
    }
}
