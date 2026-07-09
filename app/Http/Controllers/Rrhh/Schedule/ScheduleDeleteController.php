<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Schedule;

use App\Http\Controllers\MainController;
use App\Models\WorkSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScheduleDeleteController extends MainController
{
    public function delete(Request $request, int $id): RedirectResponse
    {
        $schedule = WorkSchedule::findOrFail($id);
        $schedule->delete();

        return redirect('/rrhh/schedule?user_id='.$request->get('user_id'))
            ->with(['status' => 'success', 'message' => __('Schedule entry deleted successfully')]);
    }
}
