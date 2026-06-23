<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(): View
    {
        return view('forgot-password');
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink($request->only('email_blind'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Check email!')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token): View
    {
        return view('reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $credentials = $request->only('email_blind', 'password', 'password_confirmation', 'token');

        $status = Password::reset(
            $credentials,
            function (User $user, string $password) {
                $user->password = $password;
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'New password saved!')
            : back()->withErrors(['email' => __($status)]);
    }
}
