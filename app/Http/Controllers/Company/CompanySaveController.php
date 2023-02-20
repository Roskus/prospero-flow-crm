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

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function save(Request $request)
    {
        $company = $this->companyRepository->save($request->all());

        //Save image
        if (isset($request->logo)) {
            $extension = $request->file('logo')->extension();
            $origin_path = $request->file('logo')->getPathName();

            $company_folder = Str::slug($company->name, '_');

            $destination_path = \public_path().DIRECTORY_SEPARATOR.'asset'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'company'.DIRECTORY_SEPARATOR.$company_folder;
            try {
                \mkdir($destination_path, 0775, true);
            } catch (\Exception $e) {
                Log::error('Backoffice -> Product -> Upload image: '.$destination_path);
            }

            $new_name = time().'.'.$extension;

            $origin = $origin_path;
            $destination = $destination_path.DIRECTORY_SEPARATOR.$new_name;

            if (copy($origin, $destination)) {
                $company->logo = $new_name;
            }
            $company->save();
        }

        return redirect('/company');
    }
}
