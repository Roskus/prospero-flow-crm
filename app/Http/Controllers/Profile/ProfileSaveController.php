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
use Illuminate\Support\Facades\Storage;

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

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->must_change_password = false;
        }
        $user->updated_at = now();
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time().'.'.$photo->extension();

            if ($photo->storeAs($user->photoFolder(), $filename, 'public') === false) {
                Log::error('Profile photo upload failed', ['user_id' => $user->id]);

                return redirect('/profile')->with(['status' => 'error', 'message' => 'The profile photo could not be saved.']);
            }

            $previousPhoto = $user->photo;
            $user->photo = $filename;

            if (! empty($previousPhoto) && $previousPhoto !== $filename) {
                Storage::disk('public')->delete($user->photoFolder().'/'.$previousPhoto);
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
