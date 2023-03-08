<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class EmailSaveController extends MainController
{
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $email = new Email();
            $email->created_at = now();
        } else {
            $email = Email::find($request->id);
        }
        $email->from = $request->from;
        $email->company_id = Auth::user()->company_id;
        $email->subject = $request->subject;
        $email->to = $request->to;
        $email->cc = $request->cc;
        $email->body = $request->body;
        $email->signature = (isset($request->signature)) ? isset($request->signature) : false;
        $email->updated_at = now();
        $email->status = Email::DRAFT;
        $email->save();

        if ($request->hasFile('attachment')) {
            $files = $request->file('attachment');
            $path = 'company'.DIRECTORY_SEPARATOR.'email'.DIRECTORY_SEPARATOR.Auth::user()->id;

            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $originalName = $file->getClientOriginalName();
                $newName = Uuid::uuid4()->toString().'.'.$extension;
                try {
                    $storagePath = storage_path($path.DIRECTORY_SEPARATOR.$newName);
                    $file->store($storagePath);

                    $attach = new Email\Attach();
                    $attach->email_id = $email->id;
                    $attach->original_name = $originalName;
                    $attach->file = $newName;
                    $attach->save();
                } catch (\Throwable $t) {
                    Log::error($t->getMessage());
                }
            }
        }

        return redirect('/email');
    }
}
