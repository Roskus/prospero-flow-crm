<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer\Message as CustomerMessage;
use Illuminate\Support\Facades\Auth;

class CustomerDeleteMessageController extends MainController
{
    public function delete(int $id)
    {
        $message = CustomerMessage::where('id', $id)
            ->where('author_id', Auth::id())
            ->firstOrFail();

        $message->delete();

        return back();
    }
}
