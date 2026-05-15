<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', 'in:trainee,trainer'],
            'age' => ['nullable', 'integer', 'min:10', 'max:120'],
            'gender' => ['nullable', 'string'],
            'weight' => ['nullable', 'numeric', 'min:20', 'max:300'],
            'height' => ['nullable', 'numeric', 'min:100', 'max:250'],
            'fitness_level' => ['nullable', 'string'],
            'goal' => ['nullable', 'string'],
            'equipment' => ['nullable', 'array'],
            'workout_days' => ['nullable', 'integer', 'min:1', 'max:7'],
            'workout_duration' => ['nullable', 'integer'],
            'injuries' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'trainee',
            'age' => $request->age,
            'gender' => $request->gender,
            'weight' => $request->weight,
            'height' => $request->height,
            'fitness_level' => $request->fitness_level ?? 'beginner',
            'goal' => $request->goal ?? 'general_fitness',
            'equipment' => $request->equipment,
            'workout_days' => $request->workout_days,
            'workout_duration' => $request->workout_duration,
            'injuries' => $request->injuries,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
