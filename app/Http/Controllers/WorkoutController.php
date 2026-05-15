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
            
            // Get trainer's clients
            $clients = Booking::where('trainer_id', $user->id)
                ->whereIn('status', ['confirmed', 'completed'])
                ->with('trainee')
                ->get()
                ->pluck('trainee')
                ->filter()
                ->unique('id');
        } else {
            // Trainee sees workouts assigned to them
            $workouts = Workout::where('trainee_id', $user->id)
                ->orWhere('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            $clients = collect();
        }

        $exercises = Exercise::orderBy('name')->get();
        
        // Get next workout for "Continue Plan" button
        $nextWorkoutId = Workout::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->orderBy('scheduled_date', 'asc')
            ->first()?->id;
        
        return view('workouts.index', compact('workouts', 'exercises', 'clients', 'nextWorkoutId'));
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
    $isTrainer = Auth::user()->role === 'trainer';
    
    $rules = [
        'title' => 'required|string|max:255',
        'type' => 'required|string',
        'difficulty' => 'required|string',
        'duration_minutes' => 'nullable|integer',
        'exercises' => 'required|array',
        'exercises.*.exercise_id' => 'required|exists:exercises,_id',
        'exercises.*.sets' => 'required|integer|min:1',
        'exercises.*.reps' => 'required|integer|min:1',
    ];

    if ($isTrainer) {
        $rules['trainee_id'] = 'required|exists:users,_id';
    }

    $request->validate($rules);
    
    $trainee_id = $isTrainer ? $request->trainee_id : Auth::id();
    
    $workout = Workout::create([
        'trainer_id' => $isTrainer ? Auth::id() : null,
        'trainee_id' => $trainee_id,
        'user_id' => $trainee_id, 
        'assigned_by' => $isTrainer ? Auth::id() : null,
        'title' => $request->title,
        'type' => $request->type,
        'difficulty' => $request->difficulty,
        'duration_minutes' => $request->duration_minutes,
        'exercises' => $request->exercises,
        'scheduled_date' => $request->scheduled_date ?? now(),
    ]);
    
    return redirect()->route('workouts.index')
        ->with('success', 'Workout created successfully!');
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
        $user = Auth::user();
        $workout = Workout::findOrFail($id);

        // Allow edit if trainer created it or trainee owns it (for self-created workouts)
        if (($user->role === 'trainer' && (string) $workout->trainer_id === (string) $user->id) || 
            ($user->role === 'trainee' && (string) $workout->user_id === (string) $user->id && !$workout->assigned_by)) {
            $exercises = Exercise::orderBy('name')->get();
            return view('workouts.edit', compact('workout', 'exercises'));
        }

        return redirect()->route('workouts.index')->with('error', 'Only owners can edit workouts.');
    }
    
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $workout = Workout::findOrFail($id);

        // Allow update if trainer created it or trainee owns it (for self-created workouts)
        if (($user->role === 'trainer' && (string) $workout->trainer_id === (string) $user->id) || 
            ($user->role === 'trainee' && (string) $workout->user_id === (string) $user->id && !$workout->assigned_by)) {
            
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

        return redirect()->route('workouts.index')->with('error', 'Only owners can edit workouts.');
    }
    
    public function destroy($id)
    {
        $user = Auth::user();
        $workout = Workout::findOrFail($id);

        // Allow deletion if trainer created it or trainee owns it (for self-created workouts)
        if (($user->role === 'trainer' && (string) $workout->trainer_id === (string) $user->id) || 
            ($user->role === 'trainee' && (string) $workout->user_id === (string) $user->id && !$workout->assigned_by)) {
            $workout->delete();
            return redirect()->route('workouts.index')->with('success', 'Workout deleted successfully!');
        }

        return redirect()->route('workouts.index')->with('error', 'Only owners can delete workouts.');
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
    public function useTemplate(Request $request)
    {
        $templateName = $request->template_name;
        $user = Auth::user();
        
        // Define template data
        $templates = [
            'Push Pull Legs (PPL)' => [
                'type' => 'Strength',
                'difficulty' => 'Intermediate',
                'duration_minutes' => 65,
                'exercises' => [
                    ['name' => 'Bench Press', 'sets' => 4, 'reps' => 10],
                    ['name' => 'Shoulder Press', 'sets' => 3, 'reps' => 12],
                    ['name' => 'Lateral Raises', 'sets' => 3, 'reps' => 15],
                    ['name' => 'Triceps Pushdowns', 'sets' => 3, 'reps' => 12],
                ]
            ],
            'Full Body Ignition' => [
                'type' => 'Strength',
                'difficulty' => 'Beginner',
                'duration_minutes' => 45,
                'exercises' => [
                    ['name' => 'Squats', 'sets' => 3, 'reps' => 12],
                    ['name' => 'Push Ups', 'sets' => 3, 'reps' => 15],
                    ['name' => 'Pull Ups', 'sets' => 3, 'reps' => 10],
                    ['name' => 'Plank', 'sets' => 3, 'reps' => 60],
                ]
            ],
            'HIIT Cardio Burn' => [
                'type' => 'HIIT',
                'difficulty' => 'Advanced',
                'duration_minutes' => 20,
                'exercises' => [
                    ['name' => 'Burpees', 'sets' => 4, 'reps' => 20],
                    ['name' => 'Mountain Climbers', 'sets' => 4, 'reps' => 30],
                    ['name' => 'Jump Squats', 'sets' => 4, 'reps' => 15],
                ]
            ],
            'Lower Body Focus' => [
                'type' => 'Strength',
                'difficulty' => 'Intermediate',
                'duration_minutes' => 55,
                'exercises' => [
                    ['name' => 'Deadlifts', 'sets' => 4, 'reps' => 8],
                    ['name' => 'Leg Press', 'sets' => 3, 'reps' => 12],
                    ['name' => 'Lunges', 'sets' => 3, 'reps' => 12],
                ]
            ]
        ];

        if (!isset($templates[$templateName])) {
            return redirect()->back()->with('error', 'Template not found.');
        }

        $template = $templates[$templateName];
        
        // Find exercise IDs for names
        $formattedExercises = [];
        foreach ($template['exercises'] as $exData) {
            $exercise = Exercise::where('name', 'LIKE', '%' . $exData['name'] . '%')->first();
            if ($exercise) {
                $formattedExercises[] = [
                    'exercise_id' => (string) $exercise->id,
                    'sets' => $exData['sets'],
                    'reps' => $exData['reps'],
                ];
            }
        }

        if (empty($formattedExercises)) {
            return redirect()->back()->with('error', 'No valid exercises found in this template. Please check the exercise library.');
        }

        $workout = Workout::create([
            'user_id' => $user->id,
            'trainee_id' => $user->id,
            'title' => $templateName,
            'type' => $template['type'],
            'difficulty' => $template['difficulty'],
            'duration_minutes' => $template['duration_minutes'],
            'exercises' => $formattedExercises,
            'scheduled_date' => now(),
        ]);

        return redirect()->route('workouts.index')->with('success', "Template '$templateName' added to your workouts!");
    }
}
