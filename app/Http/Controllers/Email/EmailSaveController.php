<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Http\Requests\EmailRequest;
use App\Services\EmailCreateService;

class EmailSaveController extends MainController
{
    public function save(EmailRequest $request)
    {
        $emailCreateService = new EmailCreateService();
        $emailCreateService->create($request->validated(), $request->file('attachment'));

        return redirect('/email')->with('success', __('Email saved successfully'));
    }
}
