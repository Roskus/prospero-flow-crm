<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Http\Requests\Company\CompanySaveRequest;
use App\Repositories\CompanyRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompanySaveController extends MainController
{
    public function __construct(private CompanyRepository $companyRepository) {}

    public function save(CompanySaveRequest $request)
    {
        // Authorization is now handled by FormRequest
        $company = $this->companyRepository->save($request->validated());

        if ($request->hasFile('logo')) {
            $folder = 'company/'.Str::slug($company->name, '_');
            // Use the validated MIME type: jpeg|jpg|png|webp only
            $filename = time().'.'.$request->file('logo')->extension();

            try {
                $request->file('logo')->storeAs($folder, $filename, 'public');
                $company->logo = $filename;
                $company->save();
            } catch (\Throwable $e) {
                Log::error('Company logo upload failed: '.$e->getMessage());
            }
        }

        return redirect(route('company.index'));
    }
}
