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
            'upcoming_sessions' => Booking::where('trainee_id', $trainee->id)
                ->where('status', 'confirmed')
                ->where('session_date', '>=', now()->subHours(3))
                ->count(),
        ];
        
        $recentWorkouts = Workout::where('user_id', $trainee->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $upcomingSessions = Booking::where('trainee_id', $trainee->id)
            ->where('status', 'confirmed')
            ->where('session_date', '>=', now()->subHours(3))
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

        // Latest progress for BMI card
        $latestProgress = \App\Models\ProgressMetric::where('user_id', $trainee->id)
            ->orderBy('date', 'desc')
            ->first();

        // Dummy data for weekly calorie chart
        $weeklyCalories = [2100, 2400, 1900, 2800, 2200, 2600, 3100, 2500];

        // Motivational quote
        $quotes = [
            "The only bad workout is the one that didn't happen.",
            "It never gets easier, you just get stronger.",
            "Don't stop when you're tired. Stop when you're done.",
            "Push harder than yesterday if you want a different tomorrow."
        ];
        $motivationalQuote = $quotes[array_rand($quotes)];

        // Dummy AI tip
        $aiTip = "Based on your recent sessions, adding 15 minutes of cardio after your strength training could boost your fat burn by 20%.";

        return view('trainee.dashboard', compact(
            'stats',
            'recentWorkouts',
            'upcomingSessions',
            'availableTrainers',
            'streak',
            'activityCalendar',
            'activityTotal',
            'latestProgress',
            'weeklyCalories',
            'motivationalQuote',
            'aiTip'
        ));
    }
    
    public function trainers(Request $request)
    {
        $query = User::where('role', 'trainer');
        
        // Search by name or specialization
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('specialization', 'like', '%' . $search . '%');
            });
        }
        
        // Filter by specialization
        if ($request->filled('specialization')) {
            $query->where('specialization', 'like', '%' . $request->specialization . '%');
        }
        
        // Filter by price range
        if ($request->filled('price_range')) {
            $priceRange = $request->price_range;
            if ($priceRange === '0-500') {
                $query->whereBetween('hourly_rate', [0, 500]);
            } elseif ($priceRange === '501-1000') {
                $query->whereBetween('hourly_rate', [501, 1000]);
            } elseif ($priceRange === '1000+') {
                $query->where('hourly_rate', '>=', 1000);
            }
        }
        
        $trainers = $query->paginate(12);
            
        return view('trainee.trainers', compact('trainers'));
    }
    
    public function bookTrainer($id)
    {
        $trainer = User::findOrFail($id);
        return view('bookings.create', compact('trainer'));
    }
    
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (!$query || strlen($query) < 2) {
            return redirect()->route('trainee.trainers')->with('info', 'Please enter at least 2 characters to search');
        }
        
        // Search trainers by name or specialization
        $trainers = User::where('role', 'trainer')
            ->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('specialization', 'like', '%' . $query . '%')
                  ->orWhere('bio', 'like', '%' . $query . '%');
            })
            ->paginate(12)
            ->appends(['search' => $query]);
        
        return view('trainee.trainers', compact('trainers', 'query'));
    }
}
