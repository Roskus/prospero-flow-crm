<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Email;

use App\Http\Requests\EmailRequest;
use App\Services\EmailCreateService;
use Illuminate\Http\JsonResponse;

class EmailCreateController
{
    public function create(EmailRequest $request): JsonResponse
    {
        $emailCreateService = new EmailCreateService();
        $email = $emailCreateService->create($request->validated(), $request->file('attachment'));

        return response()->json(['email' => $email], 201);
    }
}
