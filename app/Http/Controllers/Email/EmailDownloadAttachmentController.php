<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email\Attach;
use Illuminate\Support\Facades\Auth;

class EmailDownloadAttachmentController extends MainController
{
    public function downloadAttachment(int $attachmentId)
    {
        $attach = Attach::with('email')->findOrFail($attachmentId);

        abort_if($attach->email->company_id !== Auth::user()->company_id, 403);

        $filePath = storage_path('app/'.$attach->file);

        return response()->download($filePath, $attach->original_name);
    }
}
