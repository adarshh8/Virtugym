<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            'request' => $request,
            'email' => $request->session()->get('password_reset_otp.email', $request->email),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $otpData = $request->session()->get('password_reset_otp');
        $email = strtolower($request->email);

        if (!$otpData || ($otpData['email'] ?? null) !== $email) {
            throw ValidationException::withMessages([
                'email' => __('Please request a fresh OTP for this email address.'),
            ]);
        }

        if (($otpData['expires_at'] ?? 0) < now()->timestamp) {
            $request->session()->forget('password_reset_otp');

            throw ValidationException::withMessages([
                'otp' => __('This OTP has expired. Please request a new one.'),
            ]);
        }

        if (!Hash::check($request->otp, $otpData['code_hash'] ?? '')) {
            throw ValidationException::withMessages([
                'otp' => __('The OTP is incorrect.'),
            ]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => __('We could not find a user with that email address.'),
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        $request->session()->forget('password_reset_otp');

        return redirect()->route('login')->with('status', __('Your password has been changed. Please sign in with the new password.'));
    }
}
