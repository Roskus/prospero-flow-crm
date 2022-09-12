<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Company;
use Squire\Models\Country;

class CompanyController extends MainController
{


    public function add(Request $request)
    {
        $company = new Company();
        $data['company'] = $company;
        $data['countries'] = Country::all();
        return view('company.company', $data);
    }

    /**
     *
     */
    public function edit(Request $request,int $id)
    {
        $company = Company::find($id);
        $data['company'] = $company;
        $data['countries'] = Country::all();
        return view('company/company', $data);
    }

    /**
     *
     */
    public function save(Request $request)
    {
        if (empty($request->id)) {
            $company = new Company();
        } else {
            $company = Company::find($request->id);
        }
        $company->name = $request->name;
        $company->phone = $request->phone;
        $company->email = $request->email;
        $company->website = $request->website;
        $company->country_id = $request->country_id;
        $company->status = Company::ACTIVE;

        //Save image
        if (isset($request->logo)) {
            $extension = $request->file('logo')->extension();
            $origin_path = $request->file('logo')->getPathName();

            $company_folder = Str::slug($company->name,'_');

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
        $company->updated_at = now();
        $company->save();
        return redirect('/company');
    }
}
