<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompanySaveController extends MainController
{
    private CompanyRepository $companyRepository;

    public function __construct(Request $request, CompanyRepository $companyRepository)
    {
        parent::__construct($request);
        $this->companyRepository = $companyRepository;
    }

    public function save(Request $request)
    {
        $company = $this->companyRepository->save($request->all());

        // Save image
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

        return redirect('/company');
    }
}
