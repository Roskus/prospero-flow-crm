<?php

namespace App\Http\Controllers\Profile;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileSaveController
{
    /**
     * Save user profile
     *
     * @param  Request  $request HTTP request
     */
    public function save(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('user')->ignore(Auth::user())],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'timezone' => ['string', 'timezone'],
            'photo' => ['image'],
        ]);

        $status = 'success';
        $locale = $request->lang;
        $user = User::find(Auth::user()->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->lang = $locale;
        $user->timezone = $request->timezone;
        $user->phone = $request->phone;
        //Update password if change
        if (! empty($request->password) && ! empty($request->password_confirmation) && ($request->password == $request->password_confirmation)) {
            $user->password = Hash::make($request->password);
        }
        $user->updated_at = now();
        //Save image
        if (isset($request->photo)) {
            $extension = $request->file('photo')->extension();
            $origin_path = $request->file('photo')->getPathName();

            $company_folder = Str::slug(Auth::user()->company->name, '_');

            $destination_path = \public_path().DIRECTORY_SEPARATOR.'asset'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'company'.DIRECTORY_SEPARATOR.$company_folder;
            try {
                \mkdir($destination_path, 0775, true);
            } catch (\Exception $e) {
                Log::error('Backoffice -> Profile -> Upload image: '.$destination_path);
            }

            $new_name = time().'.'.$extension;

            $origin = $origin_path;
            $destination = $destination_path.DIRECTORY_SEPARATOR.$new_name;

            if (copy($origin, $destination)) {
                $user->photo = $new_name;
            }
        }
        $user->save();
        //Update current software language
        App::setLocale($locale);
        session()->put('locale', $locale);

        $response = ['status' => $status, 'message' => __('Profile saved successfully')];

        return redirect('/profile')->with($response);
    }
}
