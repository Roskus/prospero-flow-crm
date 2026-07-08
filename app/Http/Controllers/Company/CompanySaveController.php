<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompanySaveController extends MainController
{
    private const string ROUTE_INDEX = 'company.index';

    private CompanyRepository $companyRepository;

    public function __construct(Request $request, CompanyRepository $companyRepository)
    {
        parent::__construct($request);
        $this->companyRepository = $companyRepository;
    }

    public function save(Request $request)
    {
        if (empty($request->id)) {
            if (Auth::user()->cannot('create company')) {
                return redirect(route(self::ROUTE_INDEX))->with('error', __('Unauthorized'));
            }
        } else {
            if (Auth::user()->cannot('update company')) {
                return redirect(route(self::ROUTE_INDEX))->with('error', __('Unauthorized'));
            }
            if ((int) $request->id !== (int) Auth::user()->company_id) {
                return redirect(route(self::ROUTE_INDEX))->with('error', __('Unauthorized'));
            }
        }

        $company = $this->companyRepository->save($request->all());

        if ($request->hasFile('logo')) {
            $folder = 'company/'.Str::slug($company->name, '_');
            $filename = time().'.'.$request->file('logo')->extension();

            try {
                $request->file('logo')->storeAs($folder, $filename, 'public');
                $company->logo = $filename;
                $company->save();
            } catch (\Throwable $e) {
                Log::error('Company logo upload failed: '.$e->getMessage());
            }
        }

        return redirect(route(self::ROUTE_INDEX));
    }
}
