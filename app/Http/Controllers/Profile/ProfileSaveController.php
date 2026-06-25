<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\MainController;
use App\Http\Requests\ProfileSaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProfileSaveController extends MainController
{
    public function save(ProfileSaveRequest $request)
    {
        $validated = $request->validated();

        $status = 'success';
        $locale = $validated['lang'] ?? 'en';
        $user = User::find(Auth::user()->id);
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'] ?? null;
        $user->email = $validated['email'];
        $user->lang = $locale;
        $user->phone = $validated['phone'] ?? null;
        $user->timezone = $validated['timezone'] ?? null;
        $user->signature_html = ! empty($validated['signature_html'])
            ? strip_tags($validated['signature_html'], '<a><b><strong><i><em><u><br><p><div><span><ul><ol><li><img>')
            : null;

        // Update password if change
        if (! empty($validated['password']) && ! empty($validated['password_confirmation']) && ($validated['password'] === $validated['password_confirmation'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->updated_at = now();
        // Save image
        if ($request->hasFile('photo')) {
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
        // Update current software language
        App::setLocale($locale);
        session()->put('locale', $locale);

        $response = ['status' => $status, 'message' => 'Profile saved successfully'];

        return redirect('/profile')->with($response);
    }
}
