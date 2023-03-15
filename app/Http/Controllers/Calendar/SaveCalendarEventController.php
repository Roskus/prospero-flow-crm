<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\MainController;
use App\Repositories\CalendarRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SaveCalendarEventController extends MainController
{
    private $calendarRepository;

    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    public function save(Request $request): RedirectResponse
    {
        $this->calendarRepository->save($request->all());

        return back();
    }
}
