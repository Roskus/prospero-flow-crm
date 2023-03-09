<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\Email\Attach;

class EmailDownloadAttachmentController extends Controller
{
    public function downloadAttachment(int $attachmentId)
    {
        $attach = Attach::findOrFail($attachmentId);
        $filePath = storage_path('app/'.$attach->file);

        return response()->download($filePath, $attach->original_name);
    }
}
