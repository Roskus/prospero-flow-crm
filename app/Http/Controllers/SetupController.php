<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Company;

class SetupController extends Controller
{
    public function index()
    {
        return view('setup/step1');
    }
}