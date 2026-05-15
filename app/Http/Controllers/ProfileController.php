<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update profile information (name, email, fitness data, etc.)
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255'],
            'age'              => ['nullable', 'integer', 'min:10', 'max:100'],
            'gender'           => ['nullable', 'in:male,female,other'],
            'weight'           => ['nullable', 'numeric', 'min:20', 'max:300'],
            'height'           => ['nullable', 'numeric', 'min:50', 'max:250'],
            'fitness_level'    => ['nullable', 'in:beginner,intermediate,advanced,expert'],
            'goal'             => ['nullable', 'in:weight_loss,muscle_gain,endurance,flexibility,general_fitness'],
            'workout_days'     => ['nullable', 'integer', 'min:1', 'max:7'],
            'injuries'         => ['nullable', 'string', 'max:1000'],
            'bio'              => ['nullable', 'string', 'max:1000'],
            'specialization'   => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'hourly_rate'      => ['nullable', 'numeric', 'min:0'],
            'upi_id'           => ['nullable', 'string', 'max:100'],
        ]);

        $user = $request->user();
        $user->fill($request->only([
            'name', 'email', 'age', 'gender', 'weight', 'height',
            'fitness_level', 'goal', 'workout_days', 'injuries',
            'bio', 'specialization', 'experience_years', 'hourly_rate',
            'upi_id',
        ]));
        $user->equipment = $request->input('equipment', []);
        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully! ✅');
    }

    /**
     * Change password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Password changed successfully! 🔒');
    }

    /**
     * Delete account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
