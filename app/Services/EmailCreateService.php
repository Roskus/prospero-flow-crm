<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Email;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class EmailCreateService
{
    public function create(array $data, $files = null): Email
    {
        if (empty($data['id'])) {
            $email = new Email;
            $email->created_at = now();
        } else {
            $email = Email::where('company_id', Auth::user()->company_id)->find($data['id']);
        }

        $email->from = $data['from'];
        $email->from_name = ($data['from'] === Auth::user()->email)
            ? Auth::user()->first_name.' '.Auth::user()->last_name
            : Auth::user()->company->name;
        $email->company_id = Auth::user()->company_id;
        $email->subject = $data['subject'];
        $email->to = $data['to'];
        $email->cc = $data['cc'] ?? null;
        $email->bcc = $data['bcc'] ?? null;
        $email->body = $data['body'];
        $email->signature = (bool) ($data['signature'] ?? false);
        $email->updated_at = now();
        $email->status = Email::DRAFT;
        $email->save();

        if ($files) {
            $this->saveAttachments($files, $email);
        }

        return $email;
    }

    private function saveAttachments($files, Email $email): void
    {
        $companyName = Str::slug(strtolower(Auth::user()->company->name), '-');
        $DS = DIRECTORY_SEPARATOR;
        $path = 'company'.$DS.$companyName.$DS.'email'.$DS.Auth::user()->id;

        foreach ($files as $file) {
            $extension = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $newName = Uuid::uuid4()->toString().'.'.$extension;
            Storage::putFileAs($path, $file, $newName);

            $attach = new Email\Attach;
            $attach->email_id = $email->id;
            $attach->original_name = $originalName;
            $attach->file = $path.$DS.$newName;
            $attach->mime = $file->getClientMimeType();
            $attach->save();
        }
    }
}
