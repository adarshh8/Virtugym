<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($request->email);
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => __('We could not find a user with that email address.'),
            ]);
        }

        $otp = (string) random_int(100000, 999999);

        $request->session()->put('password_reset_otp', [
            'email' => $email,
            'code_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        Mail::raw("Your VirtuGym password reset OTP is {$otp}. It expires in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('VirtuGym password reset OTP');
        });

        return redirect()->route('password.otp.reset')
            ->with('status', 'We sent a 6-digit OTP to your email. Enter it below to change your password.');
    }
}
