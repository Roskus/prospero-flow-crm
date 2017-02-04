<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
//use App\Models\Season;

class MainController extends Controller
{
    public function index()
    {
        //$season = new Season();
        //$data['seasons'] = $season->getAll();
        return view('dashboard');
    }
}
