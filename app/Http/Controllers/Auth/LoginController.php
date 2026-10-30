<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt(array_merge($credentials, ['active' => true]), $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros o la cuenta está inactiva.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'Bienvenido de vuelta a EJJ.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Tu sesión se cerró correctamente.');
    }
}
