<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Workout;
use App\Models\User;
use App\Models\ExerciseLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if ($user->role === 'trainer') {
            return $this->trainerAnalytics($user);
        } else {
            return $this->traineeAnalytics($user, $request);
        }
    }
    
    private function traineeAnalytics($user, Request $request)
    {
        $workouts = Workout::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('trainee_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('id')
            ->values();

        $completedWorkoutItems = $workouts
            ->filter(fn ($workout) => !empty($workout->completed_at))
            ->values();

        $exerciseLogs = ExerciseLog::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $totalWorkouts = $workouts->count();
        $completedWorkouts = $completedWorkoutItems->count();
        $completionRate = $totalWorkouts > 0 ? round(($completedWorkouts / $totalWorkouts) * 100) : 0;

        $totalVolume = 0;
        $totalReps = 0;
        foreach ($exerciseLogs as $log) {
            $logTotals = $this->exerciseLogTotals($log);
            $totalVolume += $logTotals['volume'];
            $totalReps += $logTotals['reps'];
        }
        
<<<<<<< Updated upstream
        // Volume stats
        $totalVolume = (float) ExerciseLog::where('user_id', $user->id)->sum('weight');
        $totalReps = (int) ExerciseLog::where('user_id', $user->id)->sum('reps');
        
        // Weekly progress (simplified for MongoDB)
        $thirtyDaysAgo = now()->subDays(30);
        $weeklyWorkouts = Workout::where('user_id', $user->id)
            ->where('completed_at', '>=', $thirtyDaysAgo)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'asc')
            ->get();
        
        // Group by week manually
        $weeklyProgress = [];
        foreach ($weeklyWorkouts as $workout) {
            $weekKey = $workout->completed_at->format('Y-m');
            if (!isset($weeklyProgress[$weekKey])) {
                $weeklyProgress[$weekKey] = 0;
            }
            $weeklyProgress[$weekKey]++;
        }
        
        // Personal Records
=======
>>>>>>> Stashed changes
        $prs = ExerciseLog::where('user_id', $user->id)
            ->where('is_pr', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
<<<<<<< Updated upstream
        
=======

        $weekStarts = collect(range(7, 0))->map(fn ($weeksAgo) => now()->startOfWeek()->subWeeks($weeksAgo));
        $workoutFrequency = $weekStarts->map(function ($weekStart) use ($completedWorkoutItems) {
            $weekEnd = $weekStart->copy()->endOfWeek();

            return $completedWorkoutItems->filter(function ($workout) use ($weekStart, $weekEnd) {
                $completedAt = \Carbon\Carbon::parse($workout->completed_at);
                return $completedAt->between($weekStart, $weekEnd, true);
            })->count();
        })->values()->all();

        $volumeOverTime = $weekStarts->map(function ($weekStart) use ($exerciseLogs) {
            $weekEnd = $weekStart->copy()->endOfWeek();

            return (int) $exerciseLogs->filter(function ($log) use ($weekStart, $weekEnd) {
                $loggedAt = \Carbon\Carbon::parse($log->created_at);
                return $loggedAt->between($weekStart, $weekEnd, true);
            })->sum(fn ($log) => $this->exerciseLogTotals($log)['volume']);
        })->values()->all();

        $durationTrend = $completedWorkoutItems
            ->sortBy('completed_at')
            ->take(-8)
            ->map(fn ($workout) => (int) ($workout->duration_minutes ?? 0))
            ->filter(fn ($duration) => $duration > 0)
            ->values()
            ->all();

        $typeCounts = $workouts
            ->groupBy(fn ($workout) => $workout->type ?: 'General')
            ->map(fn ($items) => $items->count());
        $typeTotal = max($typeCounts->sum(), 1);
        $colors = ['#f43f5e', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#38bdf8'];
        $muscleBreakdown = $typeCounts
            ->sortDesc()
            ->take(6)
            ->values()
            ->map(function ($count, $index) use ($typeCounts, $typeTotal, $colors) {
                $name = $typeCounts->sortDesc()->keys()->values()->get($index);

                return [
                    'name' => ucfirst($name),
                    'value' => round(($count / $typeTotal) * 100),
                    'color' => $colors[$index % count($colors)],
                ];
            })
            ->all();

        $bestDays = collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])
            ->mapWithKeys(function ($day) use ($completedWorkoutItems) {
                return [$day => $completedWorkoutItems->filter(function ($workout) use ($day) {
                    return \Carbon\Carbon::parse($workout->completed_at)->format('D') === $day;
                })->count()];
            })
            ->all();

        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $previousMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = now()->subMonthNoOverflow()->endOfMonth();

        $currentWorkouts = $completedWorkoutItems->filter(function ($workout) use ($currentMonthStart, $currentMonthEnd) {
            return \Carbon\Carbon::parse($workout->completed_at)->between($currentMonthStart, $currentMonthEnd, true);
        })->count();
        $previousWorkouts = $completedWorkoutItems->filter(function ($workout) use ($previousMonthStart, $previousMonthEnd) {
            return \Carbon\Carbon::parse($workout->completed_at)->between($previousMonthStart, $previousMonthEnd, true);
        })->count();

        $currentVolume = $exerciseLogs->filter(function ($log) use ($currentMonthStart, $currentMonthEnd) {
            return \Carbon\Carbon::parse($log->created_at)->between($currentMonthStart, $currentMonthEnd, true);
        })->sum(fn ($log) => $this->exerciseLogTotals($log)['volume']);
        $previousVolume = $exerciseLogs->filter(function ($log) use ($previousMonthStart, $previousMonthEnd) {
            return \Carbon\Carbon::parse($log->created_at)->between($previousMonthStart, $previousMonthEnd, true);
        })->sum(fn ($log) => $this->exerciseLogTotals($log)['volume']);

        $currentCalories = (int) round($currentVolume * 0.08 + $currentWorkouts * 80);
        $previousCalories = (int) round($previousVolume * 0.08 + $previousWorkouts * 80);

        $comparison = [
            'workouts' => ['current' => $currentWorkouts, 'previous' => $previousWorkouts, 'trend' => $this->trendLabel($currentWorkouts, $previousWorkouts)],
            'volume' => ['current' => (int) $currentVolume, 'previous' => (int) $previousVolume, 'trend' => $this->trendLabel($currentVolume, $previousVolume)],
            'calories' => ['current' => $currentCalories, 'previous' => $previousCalories, 'trend' => $this->trendLabel($currentCalories, $previousCalories)],
        ];

        $plannedThisMonth = $workouts->filter(function ($workout) use ($currentMonthStart, $currentMonthEnd) {
            $date = $workout->scheduled_date ?: $workout->created_at;
            return \Carbon\Carbon::parse($date)->between($currentMonthStart, $currentMonthEnd, true);
        })->count();
        $consistencyScore = $plannedThisMonth > 0 ? min(100, round(($currentWorkouts / $plannedThisMonth) * 100)) : 0;

        $analyticsPayload = compact(
            'totalWorkouts', 'completedWorkouts', 'completionRate',
            'totalVolume', 'totalReps',
            'workoutFrequency', 'volumeOverTime', 'durationTrend',
            'muscleBreakdown', 'bestDays', 'comparison', 'consistencyScore'
        );

        if ($request->expectsJson()) {
            return response()->json($analyticsPayload + [
                'updated_at' => now()->toIso8601String(),
            ]);
        }

>>>>>>> Stashed changes
        return view('analytics.trainee', compact(
            'totalWorkouts', 'completedWorkouts', 'completionRate',
            'totalVolume', 'totalReps', 'weeklyProgress', 'prs'
        ));
    }

    private function exerciseLogTotals($log): array
    {
        $weights = is_array($log->weight) ? $log->weight : [$log->weight];
        $reps = is_array($log->reps) ? $log->reps : [$log->reps];
        $sets = max(count($weights), count($reps), (int) ($log->sets ?? 1));
        $totalReps = 0;
        $totalVolume = 0;

        for ($i = 0; $i < $sets; $i++) {
            $setReps = (int) ($reps[$i] ?? $reps[0] ?? 0);
            $setWeight = (float) ($weights[$i] ?? $weights[0] ?? 0);
            $totalReps += $setReps;
            $totalVolume += $setWeight * $setReps;
        }

        return ['reps' => $totalReps, 'volume' => $totalVolume];
    }

    private function trendLabel($current, $previous): string
    {
        if ((float) $previous === 0.0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $change = (($current - $previous) / $previous) * 100;
        return ($change > 0 ? '+' : '') . round($change) . '%';
    }
    
    private function trainerAnalytics($user)
    {
        $totalClients = Booking::where('trainer_id', $user->id)
            ->where('status', 'confirmed')
            ->distinct('trainee_id')
            ->count('trainee_id');
            
        $totalSessions = Booking::where('trainer_id', $user->id)->count();
        $totalRevenue = (float) Booking::where('trainer_id', $user->id)
            ->where('status', 'confirmed')
            ->sum('amount');
            
        $averageRating = $user->rating ?? 5.0;
        
        $upcomingSessions = Booking::where('trainer_id', $user->id)
            ->where('session_date', '>', now())
            ->count();
        
        return view('analytics.trainer', compact(
            'totalClients', 'totalSessions', 'totalRevenue',
            'averageRating', 'upcomingSessions'
        ));
    }
}
