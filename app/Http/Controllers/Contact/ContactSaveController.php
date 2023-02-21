<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\MainController;
use App\Repositories\ContactRepository;
use Illuminate\Http\Request;

class ContactSaveController extends MainController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function save(Request $request)
    {
        $contact = $this->contactRepository->save($request->all());

        return back();
    }
}
