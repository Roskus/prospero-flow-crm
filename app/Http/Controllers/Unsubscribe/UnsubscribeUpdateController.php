<?php

declare(strict_types=1);

namespace App\Http\Controllers\Unsubscribe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnsubscribeUpdateController extends Controller
{
    public function update(Request $request)
    {
        return view('unsubscribe.unsubscribe');
    }
}
