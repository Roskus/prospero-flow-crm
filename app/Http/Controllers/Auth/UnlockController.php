<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UnlockController extends Controller
{
    public function unlock(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            // Si no hay usuario autenticado, redirige al formulario de inicio de sesión.
            return redirect()->route('login');
        }

        if (Hash::check($request->password, $user->password)) {
            // Si la contraseña es correcta, autentica al usuario de nuevo.
            Auth::login($user);
            session(['locked' => false]);

            return redirect('/');
        } else {
            return back()->withErrors(['password' => __('Invalid password')]);
        }
    }
}
