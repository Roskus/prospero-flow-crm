<?php

declare(strict_types=1);

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\MainController;
use App\Repositories\ContactRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactSaveController extends MainController
{
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function save(Request $request): RedirectResponse
    {
        $contact = $this->contactRepository->save($request->all());

        $url = $contact->lead_id ? 'lead/show/'.$contact->lead_id : 'customer/show/'.$contact->customer_id;

        return redirect($url);
    }
}
