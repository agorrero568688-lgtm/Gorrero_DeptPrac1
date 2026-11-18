<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the administrator login page.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Authenticate administrator using EITHER email OR username along with password.
     */
    public function login(Request $request): RedirectResponse
    {
        // Validate login form inputs
        $credentials = $request->validate([
            'login' => ['required', 'string'], // Accepts email or username
            'password' => ['required', 'string'],
        ]);

        // Determine dynamically whether the input is a valid email or a username
        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt authentication against the resolved column
        if (Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            // Regenerate session ID to prevent session fixation attacks
            $request->session()->regenerate();

            return redirect()->intended(route('plants.index'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        // Return error feedback if authentication fails
        return back()->withErrors([
            'login' => 'The provided credentials do not match our nursery records.',
        ])->onlyInput('login');
    }

    /**
     * Securely log out the administrator and invalidate the session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // Invalidate session and regenerate CSRF token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been securely logged out.');
    }
}
