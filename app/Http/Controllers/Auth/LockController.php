<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class LockController extends Controller
{
    /**
     * Show locking screen
     */
    public function index()
    {
        // Check if the user is authenticated before show the locking screen
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        session(['locked' => true]);
        return view('auth.lock');
    }
}
