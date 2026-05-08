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
        
        return view('dashboard', compact(
            'totalWorkouts', 'completedWorkouts', 
            'totalExercisesLogged', 'totalVolume',
            'recentWorkouts', 'latestProgress', 
            'prs', 'streak', 'activityCalendar', 'activityTotal'
        ));
    }
}
