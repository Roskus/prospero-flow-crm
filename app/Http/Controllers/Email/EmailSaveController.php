<?php

declare(strict_types=1);

namespace App\Http\Controllers\Email;

use App\Http\Controllers\MainController;
use App\Http\Requests\EmailRequest;
use App\Models\Email;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class EmailSaveController extends MainController
{
    public function save(EmailRequest $request)
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
        $email->bcc = !empty($request->bcc) ? $request->bcc : null;
        $email->body = $request->body;
        $email->signature = (isset($request->signature)) ? isset($request->signature) : false;
        $email->updated_at = now();
        $email->status = Email::DRAFT;
        $email->save();

        if ($request->hasFile('attachment')) {
            $files = $request->file('attachment');
            $this->saveAttachments($files, $email);
        }

        return redirect('/email');
    }

    public function saveAttachments($files, $email)
    {
        $companyName = Str::slug(strtolower(Auth::user()->company->name), '-');
        $DS = DIRECTORY_SEPARATOR;
        $path = 'company'.$DS.$companyName.$DS.'email'.$DS.Auth::user()->id;

        foreach ($files as $file) {
            $extension = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $newName = Uuid::uuid4()->toString().'.'.$extension;
            try {
                Storage::putFileAs($path, $file, $newName);

                $attach = new Email\Attach();
                $attach->email_id = $email->id;
                $attach->original_name = $originalName;
                $attach->file = $path.$DS.$newName;
                $attach->mime = $file->getClientMimeType();
                $attach->save();
            } catch (\Throwable $t) {
                Log::error($t->getMessage());
            }
        }
    }
}
