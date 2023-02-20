<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Repositories\Lead\LeadSaveRepository;
use Illuminate\Http\Request;

class LeadSaveController extends MainController
{
    private LeadSaveRepository $leadSaveRepository;

    public function __construct(LeadSaveRepository $leadRepository)
    {
        $this->leadSaveRepository = $leadRepository;
    }

    public function save(Request $request)
    {
        $lead = $this->leadSaveRepository->save($request->all());

        $response = [
            'status' => (isset($lead)) ? 'success' : 'error',
            'message' => 'Lead :name successfully saved',
            'name' => (! empty($lead)) ? $lead->name : '',
            'id' => (! empty($lead)) ? $lead->id : null,
        ];

        return redirect('/lead')->with($response);
    }
}
