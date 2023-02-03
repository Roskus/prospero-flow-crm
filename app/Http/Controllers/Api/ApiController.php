<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    protected User|null $user = null;

    public function __construct()
    {
        try {
            $this->user = Auth::user();
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
