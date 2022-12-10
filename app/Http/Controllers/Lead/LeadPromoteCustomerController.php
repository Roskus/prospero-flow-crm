<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadPromoteCustomerController extends MainController
{
    public function promote(Request $request, int $id)
    {
        $lead = Lead::find($id);
        $customer = new Customer();
        foreach ($lead->getFillable() as $attribute) {
            $customer->{$attribute} = $lead->{$attribute};
        }
        if ($lead->status == 'in_progress') {
            $customer->status = 'open';
        }
        DB::transaction(function () use ($customer, $lead) {
            if ($customer->save()) {
                $lead->delete();
            }
        });

        return redirect('/customer')->with(['status' => 'success', 'message' => __('Lead promoted to customer successfully')]);
    }
}
