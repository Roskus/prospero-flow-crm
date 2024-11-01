<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Email;

use App\Repositories\EmailRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailCreateController
{
    public function __construct(EmailRepository $emailRepository) {}

    public function create(Request $request): JsonResponse
    {
        $status = 400;

        return response()->json(['company' => $email, $status]);
    }
}
