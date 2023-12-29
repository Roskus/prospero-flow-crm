<?php

declare(strict_types=1);

namespace App\Http\Controllers\Region;

use App\Http\Controllers\Controller;
use Squire\Models\Region;

class RegionGetAjaxController extends Controller
{
    public function index(string $country)
    {
        $regions = Region::where('country_id', $country)->get()->pluck('name', 'id');

        return response()->json(['regions' => $regions]);
    }
}
