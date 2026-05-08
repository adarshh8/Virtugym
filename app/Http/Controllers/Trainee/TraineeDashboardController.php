<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Workout;
use App\Support\ActivityStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TraineeDashboardController extends Controller
{
    public function index()
    {
        $trainee = Auth::user();
        
        $stats = [
            'total_workouts' => Workout::where('user_id', $trainee->id)->count(),
            'completed_workouts' => Workout::where('user_id', $trainee->id)->whereNotNull('completed_at')->count(),
            'total_bookings' => Booking::where('trainee_id', $trainee->id)->count(),
            'upcoming_sessions' => Booking::where('trainee_id', $trainee->id)->where('session_date', '>', now())->count(),
        ];
        
        $recentWorkouts = Workout::where('user_id', $trainee->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $upcomingSessions = Booking::where('trainee_id', $trainee->id)
            ->where('session_date', '>', now())
            ->with('trainer')
            ->orderBy('session_date', 'asc')
            ->limit(5)
            ->get();
        
        // FIXED: Get ALL trainers (not just verified)
        $availableTrainers = User::where('role', 'trainer')
            ->get();  // Removed the is_verified condition temporarily

        $activityStats = ActivityStats::forUser((string) $trainee->id);
        $streak = $activityStats['streak'];
        $activityCalendar = $activityStats['calendar'];
        $activityTotal = $activityStats['total'];
        
        return view('trainee.dashboard', compact(
            'stats',
            'recentWorkouts',
            'upcomingSessions',
            'availableTrainers',
            'streak',
            'activityCalendar',
            'activityTotal'
        ));
    }
    
    public function trainers()
    {
        // FIXED: Get ALL trainers
        $trainers = User::where('role', 'trainer')
            ->paginate(12);
            
        return view('trainee.trainers', compact('trainers'));
    }
    
    public function bookTrainer($id)
    {
        $trainer = User::findOrFail($id);
        return view('bookings.create', compact('trainer'));
    }
}
