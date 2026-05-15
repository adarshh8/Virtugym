<?php
namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WithdrawalRequest;
class TrainerDashboardController extends Controller
{
    public function index()
{
    $trainer = Auth::user();
    
    // Get upcoming bookings
    $upcomingBookings = Booking::where('trainer_id', $trainer->id)
        ->where('status', 'confirmed')
        ->where('session_date', '>=', now()->subHours(3))
        ->with('trainee')
        ->orderBy('session_date', 'asc')
        ->limit(5)
        ->get();
    
    // Get recent workouts assigned by this trainer
    $myWorkouts = Workout::where('trainer_id', $trainer->id)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
        $totalEarnings = Booking::where('trainer_id', $trainer->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->get()
            ->sum('amount');
            
        $totalWithdrawn = WithdrawalRequest::where('trainer_id', $trainer->id)
            ->where('status', 'completed')
            ->get()
            ->sum('amount');
            
        $stats = [
            'total_clients' => Booking::where('trainer_id', $trainer->id)
                ->where('status', 'confirmed')
                ->distinct('trainee_id')
                ->count('trainee_id'),
            'total_sessions' => Booking::where('trainer_id', $trainer->id)->count(),
            'completed_sessions' => Booking::where('trainer_id', $trainer->id)
                ->where('status', 'completed')
                ->count(),
            'total_earned' => $totalEarnings - $totalWithdrawn,
        'upcoming_sessions' => $upcomingBookings->count(),
        'rating' => $trainer->rating ?? 0,
    ];
    
    return view('trainer.dashboard', compact('stats', 'upcomingBookings', 'myWorkouts'));
}
    public function clients()
    {
        $clients = Booking::where('trainer_id', Auth::id())
            ->where('status', 'active')
            ->with('trainee')
            ->get();
            
        return view('trainer.clients', compact('clients'));
    }
    
    public function schedule()
    {
        $bookings = Booking::where('trainer_id', Auth::id())
            ->where('session_date', '>', now())
            ->with('trainee')
            ->orderBy('session_date', 'asc')
            ->paginate(20);
            
        return view('trainer.schedule', compact('bookings'));
    }
    
    public function updateProfile(Request $request)
    {
        $trainer = Auth::user();
        
        $request->validate([
            'bio' => 'nullable|string',
            'experience_years' => 'nullable|integer',
            'specialization' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric',
            'certifications' => 'nullable|string',
        ]);
        
        $trainer->update($request->only(['bio', 'experience_years', 'specialization', 'hourly_rate', 'certifications']));
        
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
    public function withdrawalRequests()
{
    $requests = WithdrawalRequest::where('trainer_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->get();
    
    $totalEarnings = Booking::where('trainer_id', Auth::id())
        ->whereIn('status', ['confirmed', 'completed'])
        ->sum('amount');
    
    $totalWithdrawn = WithdrawalRequest::where('trainer_id', Auth::id())
        ->where('status', 'completed')
        ->get()
        ->sum('amount');
        
    $hasPending = WithdrawalRequest::where('trainer_id', Auth::id())
        ->where('status', 'pending')
        ->exists();
    
    $availableBalance = $totalEarnings - $totalWithdrawn;
    
    return view('trainer.withdrawals', compact('requests', 'totalEarnings', 'totalWithdrawn', 'availableBalance', 'hasPending'));
}

public function requestWithdrawal(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:100|max:50000',
        'upi_id' => 'required|string'
    ]);
    
    $hasPending = WithdrawalRequest::where('trainer_id', Auth::id())
        ->where('status', 'pending')
        ->exists();
        
    if ($hasPending) {
        return redirect()->back()->with('error', 'You already have a pending withdrawal request. Please wait for the admin to process it.');
    }
    
    $totalEarnings = Booking::where('trainer_id', Auth::id())
        ->whereIn('status', ['confirmed', 'completed'])
        ->sum('amount');
    
    $totalWithdrawn = WithdrawalRequest::where('trainer_id', Auth::id())
        ->where('status', 'completed')
        ->get()
        ->sum('amount');
    
    $availableBalance = $totalEarnings - $totalWithdrawn;
    
    if ($request->amount > $availableBalance) {
        return redirect()->back()->with('error', 'Insufficient balance.');
    }
    
    WithdrawalRequest::create([
        'trainer_id' => Auth::id(),
        'amount' => (float) $request->amount,
        'upi_id' => $request->upi_id,
        'status' => 'pending'
    ]);
    
    return redirect()->back()->with('success', 'Withdrawal request submitted successfully. Admin will review it.');
}
}
