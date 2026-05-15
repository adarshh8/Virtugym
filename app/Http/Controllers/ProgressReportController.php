<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workout;
use App\Models\WaterIntake;
use App\Models\ExerciseLog;
use App\Models\ProgressMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProgressReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // If trainer is viewing, they might specify a client_id
        if ($user->role === 'trainer' && $request->has('client_id')) {
            $user = User::findOrFail($request->client_id);
        }

        $startDate = Carbon::now()->subDays($request->get('days', 30))->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // 1. Workout History
        $workouts = Workout::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Water Intake Summary
        $waterIntake = WaterIntake::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
            
        $avgWater = $waterIntake->count() > 0 ? $waterIntake->sum('amount_ml') / $request->get('days', 30) : 0;

        // 3. Performance Metrics
        $logs = ExerciseLog::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
            
        $totalVolume = $logs->sum(function($log) {
            $weights = is_array($log->weight) ? $log->weight : [$log->weight];
            $reps = is_array($log->reps) ? $log->reps : [$log->reps];
            $vol = 0;
            foreach($weights as $i => $w) {
                $vol += (float)$w * (int)($reps[$i] ?? 0);
            }
            return $vol;
        });

        // 4. Weight/Body Progress
        $metrics = ProgressMetric::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('progress.report', compact(
            'user', 'workouts', 'waterIntake', 'avgWater', 
            'totalVolume', 'metrics', 'startDate', 'endDate'
        ));
    }
}
