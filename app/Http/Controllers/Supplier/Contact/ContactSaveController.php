<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier\Contact;

use App\Http\Controllers\MainController;
use App\Http\Requests\ContactSaveRequest;
use App\Repositories\SupplierContactRepository;
use Illuminate\Http\RedirectResponse;

class ContactSaveController extends MainController
{
    private SupplierContactRepository $contactRepository;

    public function __construct(SupplierContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function save(ContactSaveRequest $request): RedirectResponse
    {
        $contact = $this->contactRepository->save($request->all());

        $url = 'supplier/update/'.$contact->supplier_id;

        return redirect($url);
    }
}
