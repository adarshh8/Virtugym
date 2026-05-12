<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\ProgressMetric;
use App\Models\ExerciseLog;
use App\Support\ActivityStats;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Stats
        $totalWorkouts = Workout::where('user_id', $user->id)->count();
        $completedWorkouts = Workout::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->count();
        
        $totalExercisesLogged = ExerciseLog::where('user_id', $user->id)->count();
        $totalVolume = ExerciseLog::where('user_id', $user->id)->sum('weight');
        
        // Recent workouts
        $recentWorkouts = Workout::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Latest progress
        $latestProgress = ProgressMetric::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();
        
        // Personal Records
        $prs = ExerciseLog::where('user_id', $user->id)
            ->where('is_pr', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $activityStats = ActivityStats::forUser((string) $user->id);
        $streak = $activityStats['streak'];
        $activityCalendar = $activityStats['calendar'];
        $activityTotal = $activityStats['total'];

        // Booking stats
        $totalBookings = \App\Models\Booking::where('trainee_id', $user->id)
            ->orWhere('trainer_id', $user->id)
            ->count();
        $upcomingSessions = \App\Models\Booking::where(function($q) use ($user) {
                $q->where('trainee_id', $user->id)->orWhere('trainer_id', $user->id);
            })
            ->where('status', 'confirmed')
            ->where('start_time', '>', now())
            ->count();
        
        // Dummy data for charts (to be implemented with real logic later if needed)
        $weeklyWorkouts = [2, 4, 3, 5, 3, 4, 6, 4]; // Past 8 weeks
        $workoutTypes = [
            'Strength' => 45,
            'Cardio' => 30,
            'Flexibility' => 15,
            'HIIT' => 10
        ];
        
        return view('dashboard', compact(
            'totalWorkouts', 'completedWorkouts', 
            'totalExercisesLogged', 'totalVolume',
            'recentWorkouts', 'latestProgress', 
            'prs', 'streak', 'activityCalendar', 'activityTotal',
            'totalBookings', 'upcomingSessions', 'weeklyWorkouts', 'workoutTypes'
        ));
    }
}
