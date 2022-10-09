<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserSaveController extends MainController
{
    /**
     * @param  Request  $request
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:50',
            'email' => 'required|email|max:254',
            'lang' => 'required',
            //'password_confirmation' => 'confirmed:password'
        ]);

        if ($validator->fails()) {
            return redirect('/user')
                ->withErrors($validator)
                ->withInput();
        }

        if (empty($request->id)) {
            $user = new User();
            $user->created_at = now();
            //Company should be assigned on create
            $user->company_id = Auth::user()->company_id;
        } else {
            $user = User::find($request->id);
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->lang = $request->lang;
        $user->updated_at = now();
        if (! empty($request->password) && ! empty($request->password_confirmation) && ($request->password == $request->password_confirmation)) {
            $user->password = Hash::make($request->password);
        }
        $user->updated_at = now();
        $user->save();

        return redirect('/user');
    }
}
