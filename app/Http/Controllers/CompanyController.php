<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Squire\Models\Country;

class CompanyController extends MainController
{
    public function index(Request $request)
    {
        $company = new Company();
        $data['companies'] = $company->getAll();
        return view('company.index', $data);
    }

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
        $company->save();
        return redirect('/company');
    }
}
