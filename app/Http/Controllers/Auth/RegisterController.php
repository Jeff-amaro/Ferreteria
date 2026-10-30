<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'dni' => ['nullable', 'digits:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'dni' => $data['dni'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => UserRole::Cliente,
            'active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('catalog.index')->with('success', 'Tu cuenta fue creada. ¡Bienvenido a EJJ!');
    }
}
