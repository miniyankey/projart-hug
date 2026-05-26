<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthController extends Controller
{
    /**
     * Display the admin login form.
     */
    public function showLogin(): Response
    {
        return Inertia::render('Admin/Login');
    }

    /**
     * Handle an admin login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.index'));
        }

        return back()->withErrors([
            'email' => 'Ces identifiants ne correspondent à aucun compte.',
        ]);
    }

    /**
     * Display the admin register form.
     */
    public function showRegister(): Response
    {
        return Inertia::render('Admin/Register');
    }

    /**
     * Handle an admin register attempt.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email'],
            // password default fait en sorte que le mot de passe doit faire au moins 8 caractères, contenir une majuscule, une minuscule, un chiffre et un symbole
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Admin::create($validated);

        return redirect()->route('admin.index')->with('success', 'Compte administrateur créé avec succès.');
    }

    /**
     * Log the admin out and invalidate the session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
