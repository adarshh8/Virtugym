<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
public function index()
{
    $user = Auth::user();
    
    if ($user->role === 'trainer') {
        // Trainer sees workouts they created
        $workouts = Workout::where('trainer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    } else {
        // Trainee sees workouts assigned to them
        $workouts = Workout::where('trainee_id', $user->id)
            ->orWhere('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
    
    return view('workouts.index', compact('workouts'));
}
    
    public function create()
    {
        if (Auth::user()->role !== 'trainer') {
            return redirect()->route('workouts.index')->with('error', 'Only trainers can create workouts.');
        }

        $exercises = Exercise::orderBy('name')->get();
        
        // Get trainer's clients
        $clients = Booking::where('trainer_id', Auth::id())
            ->whereIn('status', ['confirmed', 'completed'])
            ->with('trainee')
            ->get()
            ->pluck('trainee')
            ->filter()
            ->unique('id');

        return view('workouts.create', compact('exercises', 'clients'));
    }
    
    public function store(Request $request)
{
    if (Auth::user()->role !== 'trainer') {
        return redirect()->route('workouts.index')->with('error', 'Only trainers can create workouts.');
    }

    $request->validate([
        'trainee_id' => 'required|exists:users,_id',
        'title' => 'required|string|max:255',
        'type' => 'required|string',
        'difficulty' => 'required|string',
        'duration_minutes' => 'nullable|integer',
        'exercises' => 'required|array',
        'exercises.*.exercise_id' => 'required|exists:exercises,_id',
        'exercises.*.sets' => 'required|integer|min:1',
        'exercises.*.reps' => 'required|integer|min:1',
        'exercises.*.target_weight' => 'nullable|numeric',
    ]);
    
    $workout = Workout::create([
        'trainer_id' => Auth::id(),
        'trainee_id' => $request->trainee_id,
        'user_id' => $request->trainee_id, // For trainee to view
        'assigned_by' => Auth::id(),
        'title' => $request->title,
        'type' => $request->type,
        'difficulty' => $request->difficulty,
        'duration_minutes' => $request->duration_minutes,
        'exercises' => $request->exercises,
        'scheduled_date' => $request->scheduled_date ?? now(),
    ]);
    
    return redirect()->route('workouts.show', $workout->id)
        ->with('success', 'Workout assigned to trainee successfully!');
}
    
    public function show($id)
    {
        $user = Auth::user();
        
        // Both trainer and trainee should be able to view it
        $workoutQuery = Workout::where(function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('trainer_id', $user->id);
        });

        $workout = $workoutQuery->findOrFail($id);
        
        // Get exercise details
        $exercises = [];
        foreach ($workout->exercises as $exerciseData) {
            $exercise = Exercise::find($exerciseData['exercise_id']);
            if ($exercise) {
                $exercises[] = (object)[
                    'exercise' => $exercise,
                    'sets' => $exerciseData['sets'],
                    'reps' => $exerciseData['reps'],
                    'target_weight' => $exerciseData['target_weight'] ?? null,
                ];
            }
        }
        
        // Get existing logs for this workout
        $logs = ExerciseLog::where('workout_id', $workout->id)
            ->where('user_id', $workout->user_id) // Get trainee's logs
            ->get()
            ->keyBy('exercise_id');
        
        return view('workouts.show', compact('workout', 'exercises', 'logs'));
    }
    
    public function edit($id)
    {
        if (Auth::user()->role !== 'trainer') {
            return redirect()->route('workouts.index')->with('error', 'Only trainers can edit workouts.');
        }

        $workout = Workout::where('trainer_id', Auth::id())->findOrFail($id);
        $exercises = Exercise::orderBy('name')->get();
        return view('workouts.edit', compact('workout', 'exercises'));
    }
    
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'trainer') {
            return redirect()->route('workouts.index')->with('error', 'Only trainers can edit workouts.');
        }

        $workout = Workout::where('trainer_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'difficulty' => 'required|string',
            'duration_minutes' => 'nullable|integer',
            'exercises' => 'required|array',
            'exercises.*.exercise_id' => 'required|exists:exercises,_id',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|integer|min:1',
            'exercises.*.target_weight' => 'nullable|numeric',
        ]);
        
        $workout->update($request->only(['title', 'type', 'difficulty', 'duration_minutes', 'exercises']));
        
        return redirect()->route('workouts.show', $workout->id)
            ->with('success', 'Workout updated successfully!');
    }
    
    public function destroy($id)
    {
        if (Auth::user()->role !== 'trainer') {
            return redirect()->route('workouts.index')->with('error', 'Only trainers can delete workouts.');
        }

        $workout = Workout::where('trainer_id', Auth::id())->findOrFail($id);
        $workout->delete();
        
        return redirect()->route('workouts.index')
            ->with('success', 'Workout deleted successfully!');
    }
    
    public function complete(Request $request, $id)
    {
        // Trainee completes the workout
        $workout = Workout::where('user_id', Auth::id())->findOrFail($id);
        $workout->update([
            'completed_at' => now(),
            'notes' => $request->notes,
            'rating' => $request->rating,
        ]);
        
        return redirect()->route('workouts.show', $workout->id)
            ->with('success', 'Great job! Workout completed! 🎉 Analytics have been updated.');
    }
}
