<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Razorpay\Api\Api;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_trainers' => User::where('role', 'trainer')->count(),
            'total_trainees' => User::where('role', 'trainee')->count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_trainers' => User::where('role', 'trainer')->where('is_verified', false)->count(),
            'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
            'total_withdrawn' => WithdrawalRequest::where('status', 'completed')->get()->sum('amount'),
        ];
        
        $recentUsers = User::orderBy('created_at', 'desc')->limit(10)->get();
        $recentBookings = Booking::with(['trainee', 'trainer'])->orderBy('created_at', 'desc')->limit(10)->get();
        
        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentBookings'));
    }
    
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }
    
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting admin
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot delete admin user.');
        }
        
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }
    
    public function blockUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = !($user->is_blocked ?? false);
        $user->save();
        
        $status = $user->is_blocked ? 'blocked' : 'unblocked';
        return redirect()->back()->with('success', "User {$status} successfully.");
    }
    
    public function trainers()
    {
        $trainers = User::where('role', 'trainer')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.trainers', compact('trainers'));
    }
    
    public function verifyTrainer($id)
    {
        $trainer = User::findOrFail($id);
        $trainer->is_verified = true;
        $trainer->save();
        
        return redirect()->back()->with('success', 'Trainer verified successfully!');
    }
    
    public function unverifyTrainer($id)
    {
        $trainer = User::findOrFail($id);
        $trainer->is_verified = false;
        $trainer->save();
        
        return redirect()->back()->with('success', 'Trainer unverified.');
    }
    
    public function deleteTrainer($id)
    {
        $trainer = User::findOrFail($id);
        $trainer->delete();
        
        return redirect()->back()->with('success', 'Trainer deleted successfully.');
    }
    
    public function bookings()
    {
        $bookings = Booking::with(['trainee', 'trainer'])->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.bookings', compact('bookings'));
    }

    public function processBookingRefund($id)
    {
        $booking = Booking::with(['trainee', 'trainer'])->findOrFail($id);

        if (($booking->refund_status ?? null) !== 'pending_admin') {
            return redirect()->back()->with('error', 'This booking does not have a pending admin refund request.');
        }

        $amount = (float) ($booking->refund_amount ?? $booking->amount ?? 0);
        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Refund amount is missing for this booking.');
        }

        $upiId = $booking->trainee->upi_id ?? null;

        if (!$upiId) {
            return redirect()->back()->with('error', 'Ask the trainee to add their UPI ID in profile before approving this refund.');
        }

        $trainerBalance = $this->availableTrainerBalance((string) $booking->trainer_id);
        if ($amount > $trainerBalance) {
            return redirect()->back()->with('error', "Trainer wallet has insufficient balance. Available: ₹{$trainerBalance}.");
        }

        $payment = Payment::where('booking_id', $booking->id)->first();
        $refundReference = 'ADMIN-REFUND-' . strtoupper(substr(md5($booking->id . now()), 0, 10));

        try {
            if (config('services.razorpay.key') && config('services.razorpay.secret') && $booking->payment_id) {
                $razorpay = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $refund = $razorpay->payment
                    ->fetch($booking->payment_id)
                    ->refund(['amount' => (int) round($amount * 100)]);

                $refundReference = $refund->id ?? $refundReference;
            }

            $booking->update([
                'refund_status' => 'processed',
                'refund_amount' => $amount,
                'refund_upi_id' => $upiId,
                'refund_reference' => $refundReference,
                'refund_processed_at' => now(),
                'refund_error' => null,
            ]);

            if ($payment) {
                $payment->update([
                    'status' => 'refunded',
                    'refund_amount' => $amount,
                    'refund_reference' => $refundReference,
                    'refunded_at' => now(),
                    'refund_error' => null,
                ]);
            }

            $traineeName = $booking->trainee->name ?? 'the trainee';
            $trainerName = $booking->trainer->name ?? 'trainer';

            return redirect()->back()->with('success', "Refund of ₹{$amount} approved for {$traineeName}. It has been deducted from {$trainerName}'s wallet.");
        } catch (\Exception $e) {
            \Log::error('Admin booking refund failed: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
            ]);

            $booking->update([
                'refund_status' => 'failed',
                'refund_error' => $e->getMessage(),
            ]);

            if ($payment) {
                $payment->update([
                    'refund_error' => $e->getMessage(),
                ]);
            }

            return redirect()->back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }
    
    public function deleteBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        
        return redirect()->back()->with('success', 'Booking deleted successfully.');
    }

    private function availableTrainerBalance(string $trainerId): float
    {
        // Include all bookings: confirmed, completed, and cancelled
        $earned = Booking::where('trainer_id', $trainerId)
            ->whereIn('status', ['confirmed', 'completed', 'cancelled'])
            ->sum('amount');

        // Only deduct processed refunds (not pending)
        $processedTrainerRefunds = Booking::where('trainer_id', $trainerId)
            ->where('status', 'cancelled')
            ->where('cancellation_policy', 'trainer_refund')
            ->where('refund_status', 'processed')
            ->sum('refund_amount');

        $withdrawn = WithdrawalRequest::where('trainer_id', $trainerId)
            ->where('status', 'completed')
            ->sum('amount');

        return max(0, round((float) $earned - (float) $processedTrainerRefunds - (float) $withdrawn, 2));
    }
    
    // Withdrawal Requests
    public function withdrawals()
    {
        $requests = WithdrawalRequest::with('trainer')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.withdrawals', compact('requests'));
    }
    
    public function approveWithdrawal($id)
    {
        $request = WithdrawalRequest::findOrFail($id);
        $request->status = 'completed';
        $request->approved_at = now();
        $request->save();
        
        return redirect()->back()->with('success', "Withdrawal of ₹{$request->amount} approved. Amount marked as transferred.");
    }
    
    public function rejectWithdrawal($id)
    {
        $request = WithdrawalRequest::findOrFail($id);
        $request->status = 'rejected';
        $request->save();
        
        return redirect()->back()->with('success', 'Withdrawal request rejected.');
    }
}
