<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use App\Models\Calendar;
use Illuminate\Http\Request;

class UpdateCalendarEventController extends MainController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        return Calendar::find($id);
    }
}
