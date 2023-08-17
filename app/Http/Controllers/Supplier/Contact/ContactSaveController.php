<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier\Contact;

use App\Http\Controllers\MainController;
use App\Repositories\SupplierContactRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactSaveController extends MainController
{
    private SupplierContactRepository $contactRepository;

    public function __construct(SupplierContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function save(Request $request): RedirectResponse
    {
        $contact = $this->contactRepository->save($request->all());

        $url = 'supplier/update/'.$contact->supplier_id;

        return redirect($url);
    }
}
