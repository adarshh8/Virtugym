<?php

namespace App\Http\Controllers;

use App\Models\WaterIntake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WaterIntakeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        $intakes = WaterIntake::where('user_id', $user->id)
            ->where('date', $today)
            ->get();
            
        $totalToday = $intakes->sum('amount_ml');
        $goal = 3000; // Default goal: 3L
        $percentage = min(100, round(($totalToday / $goal) * 100));
        
        $history = WaterIntake::where('user_id', $user->id)
            ->where('date', '>=', Carbon::today()->subDays(7))
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->date)->format('D');
            })
            ->map(function($day) {
                return $day->sum('amount_ml');
            });

        return view('water.index', compact('totalToday', 'goal', 'percentage', 'history', 'intakes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount_ml' => 'required|integer|min:50|max:2000',
        ]);

        WaterIntake::create([
            'user_id' => Auth::id(),
            'amount_ml' => $request->amount_ml,
            'date' => Carbon::today(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Water intake added!');
    }
}
