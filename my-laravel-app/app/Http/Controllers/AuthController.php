<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $remember = $request->boolean('remember');

        unset($validated['remember']);

        $attemptData = [
            'email_blind' => $validated['email_blind'],
            'password'    => $validated['password'],
        ];

        if (Auth::attempt($attemptData, $remember)) {
            $request->session()->regenerate();

            return redirect()->route('welcome')
                ->with('success', 'You are now logged in!');
        }

        return redirect()->back()->with('error', 'Invalid email or password');

    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create($validated);

        $remember = $request->boolean('remember');

        Auth::login($user, $remember);

        $request->session()->regenerate();

        event(new Registered($user));

        return redirect()->route('verification.notice')
            ->with('success', 'Look to your email!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login.form')
            ->with('success', 'You are now logged out!');
    }

    public function showRegistrationForm(): View
    {
        return view('register');
    }

    public function showLoginForm(): View
    {
        return view('login');
    }
}
