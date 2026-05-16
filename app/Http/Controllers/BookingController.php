<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use App\Models\TrainerAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;

class BookingController extends Controller
{
    private $razorpay;
    
    /**
     * Constructor to initialize Razorpay
     */
    public function __construct()
    {
        // Initialize Razorpay API
        if (config('services.razorpay.key') && config('services.razorpay.secret')) {
            try {
                $this->razorpay = new Api(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret')
                );
            } catch (\Exception $e) {
                \Log::error('Razorpay initialization failed: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Show booking form
     * GET /book-trainer/{id}
     */
    public function create($trainer_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        if (Auth::user()->role !== 'trainee') {
            abort(403, 'Only trainees can book trainers');
        }
        
        $trainer = User::where('role', 'trainer')->findOrFail($trainer_id);
            
        return view('bookings.create', compact('trainer'));
    }
    
    /**
     * Initiate payment and create booking with availability check
     * POST /initiate-payment/{trainer_id}
     */
    public function initiatePayment(Request $request, $trainer_id)
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    $validator = Validator::make($request->all(), [
        'session_date' => 'required|date|after:today',
        'session_time' => 'required',
        'duration' => 'required|integer|min:30|max:120',
    ]);
    
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }
    
    $trainer = User::where('role', 'trainer')->findOrFail($trainer_id);
    $amount = round((float) ($trainer->hourly_rate ?: 500) * ((int) $request->duration / 60), 2);
    
    // ===== AVAILABILITY CHECK (MongoDB Compatible) =====
    $dayOfWeek = date('w', strtotime($request->session_date));
    $sessionTime = $request->session_time;
    
    // Get all availability slots for this trainer on this day
    // Check if trainer has availability for this time slot
    $availabilities = TrainerAvailability::where('trainer_id', $trainer_id)
        ->where('day_of_week', (int)$dayOfWeek)
        ->get();
    
    $isAvailable = false;
    $availabilitySlot = null;
    foreach ($availabilities as $slot) {
        $startTime = date('H:i', strtotime($slot->start_time));
        $endTime = date('H:i', strtotime($slot->end_time));
        
        if ($sessionTime >= $startTime && $sessionTime <= $endTime) {
            $isAvailable = true;
            $availabilitySlot = $slot;
            break;
        }
    }
    
    if (!$isAvailable) {
        return redirect()->back()->with('error', 'Trainer is not available at this time. Please select another time slot.');
    }
    
    // Check if this time slot is already booked for this specific date
    $sessionDateTime = $request->session_date . ' ' . $sessionTime;
    $existingBooking = Booking::where('trainer_id', $trainer_id)
        ->whereDate('session_date', $request->session_date)
        ->where('status', 'confirmed')
        ->get()
        ->filter(function($booking) use ($sessionTime) {
            return date('H:i', strtotime($booking->session_date)) == $sessionTime;
        })
        ->count();
    
    if ($existingBooking > 0) {
        return redirect()->back()->with('error', 'This time slot is already booked. Please select another time.');
    }
    // ===== END AVAILABILITY CHECK =====
    
    // ===== INITIALIZE PAYMENT (Razorpay) =====
    // Store pending booking details in session
    session([
        'pending_booking' => [
            'trainer_id' => $trainer_id,
            'availability_id' => (string)$availabilitySlot->id,
            'session_date' => $request->session_date,
            'session_time' => $request->session_time,
            'duration' => $request->duration,
            'amount' => $amount
        ]
    ]);
    
    if (!$this->razorpay) {
        return redirect()->back()->with('error', 'Payment gateway is not configured properly.');
    }
    
    try {
        // Create Razorpay Order
        $orderData = [
            'receipt'         => 'rcptid_' . time(),
            'amount'          => (int) round($amount * 100), // Amount in paise
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];
        
        $razorpayOrder = $this->razorpay->order->create($orderData);
        
        return view('payments.razorpay', [
            'order' => $razorpayOrder,
            'trainer' => $trainer,
            'amount' => $amount,
            'razorpay_key' => config('services.razorpay.key')
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Razorpay Order Creation Failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Payment initialization failed: ' . $e->getMessage());
    }
}
    
    /**
     * Handle payment success (Real Razorpay)
     * POST /payment-success
     */
    public function paymentSuccess(Request $request)
    {
        $pendingBooking = session('pending_booking');
        
        if (!$pendingBooking) {
            return redirect()->route('trainee.trainers')
                ->with('error', 'Booking session expired.');
        }
        
        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];
        
        try {
            $this->razorpay->utility->verifyPaymentSignature($attributes);
            
            $sessionDateTime = $pendingBooking['session_date'] . ' ' . $pendingBooking['session_time'];
            $dayOfWeek = date('w', strtotime($pendingBooking['session_date']));
            $sessionTime = date('H:i', strtotime($pendingBooking['session_time']));
            
            // Get availability slot
            $availabilities = TrainerAvailability::where('trainer_id', $pendingBooking['trainer_id'])
                ->where('day_of_week', (int)$dayOfWeek)
                ->get();
            
            $availability = null;
            foreach ($availabilities as $slot) {
                $startTime = date('H:i', strtotime($slot->start_time));
                $endTime = date('H:i', strtotime($slot->end_time));
                
                if ((string)$slot->id === ($pendingBooking['availability_id'] ?? '') && $sessionTime >= $startTime && $sessionTime <= $endTime) {
                    $availability = $slot;
                    break;
                }
            }

            if (!$availability) {
                session()->forget('pending_booking');

                return redirect()->route('trainee.trainers')
                    ->with('error', 'This time slot has already been booked. Please select another slot.');
            }

            $existingBooking = Booking::where('trainer_id', $pendingBooking['trainer_id'])
                ->whereDate('session_date', $pendingBooking['session_date'])
                ->where('status', 'confirmed')
                ->get()
                ->filter(function($booking) use ($sessionTime) {
                    return date('H:i', strtotime($booking->session_date)) == $sessionTime;
                })
                ->count();

            if ($existingBooking > 0) {
                session()->forget('pending_booking');

                return redirect()->route('trainee.trainers')
                    ->with('error', 'This time slot has already been booked. Please select another slot.');
            }
            
            $booking = Booking::create([
                'trainee_id' => Auth::id(),
                'trainer_id' => $pendingBooking['trainer_id'],
                'session_date' => $sessionDateTime,
                'duration_minutes' => $pendingBooking['duration'],
                'amount' => $pendingBooking['amount'],
                'status' => 'confirmed',
                'payment_id' => $request->razorpay_payment_id
            ]);
            
            Payment::create([
                'user_id' => Auth::id(),
                'booking_id' => $booking->id,
                'amount' => $pendingBooking['amount'],
                'currency' => 'INR',
                'status' => 'completed',
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature,
                'paid_at' => now()
            ]);
            
            // Send email confirmation
            $this->sendBookingConfirmation($booking);
            
            session()->forget('pending_booking');
            
            return redirect()->route('bookings.index')
                ->with('success', 'Payment successful! Booking confirmed. Check your email.');
                
        } catch (\Exception $e) {
            \Log::error('Payment verification failed: ' . $e->getMessage());
            return redirect()->route('trainee.trainers')
                ->with('error', 'Payment verification failed. Please try again.');
        }
    }
    
    /**
     * Send email confirmation to both parties
     */
    private function sendBookingConfirmation($booking)
    {
        $trainee = User::find($booking->trainee_id);
        $trainer = User::find($booking->trainer_id);
        
        try {
            Mail::send('emails.booking-confirmation', [
                'trainee' => $trainee,
                'trainer' => $trainer,
                'booking' => $booking
            ], function($message) use ($trainee) {
                $message->to($trainee->email, $trainee->name)
                        ->subject('Booking Confirmation - VirtuGym');
            });
            
            Mail::send('emails.booking-confirmation-trainer', [
                'trainee' => $trainee,
                'trainer' => $trainer,
                'booking' => $booking
            ], function($message) use ($trainer) {
                $message->to($trainer->email, $trainer->name)
                        ->subject('New Booking Notification - VirtuGym');
            });
            
            \Log::info('Booking confirmation emails sent');
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle payment failure
     * GET /payment-failed
     */
    public function paymentFailed()
    {
        session()->forget('pending_booking');
        return redirect()->route('trainee.trainers')
            ->with('error', 'Payment failed. Please try again.');
    }
    
    /**
     * Display all bookings
     * GET /bookings
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $userId = Auth::id();
        $isTrainer = Auth::user()->role === 'trainer';
        $roleField = $isTrainer ? 'trainer_id' : 'trainee_id';
        $withRelation = $isTrainer ? 'trainee' : 'trainer';

        // 1. Upcoming/active bookings. Keep started sessions visible so the join
        // button does not disappear while the session is in progress.
        $upcomingBookings = Booking::where($roleField, $userId)
            ->where('status', 'confirmed')
            ->where('session_date', '>=', now()->subHours(3))
            ->with($withRelation)
            ->orderBy('session_date', 'asc')
            ->get();

        // 2. Past Bookings (Completed status OR confirmed but date has passed)
        $pastBookings = Booking::where($roleField, $userId)
            ->where(function($query) {
                $query->where('status', 'completed')
                          ->orWhere(function($q) {
                              $q->where('status', 'confirmed')
                                ->where('session_date', '<', now()->subHours(3));
                      });
            })
            ->with($withRelation)
            ->orderBy('session_date', 'desc')
            ->get();

        // 3. Cancelled Bookings
        $cancelledBookings = Booking::where($roleField, $userId)
            ->where('status', 'cancelled')
            ->with($withRelation)
            ->orderBy('updated_at', 'desc')
            ->get();

        // 4. Summary Metrics (For Trainees)
        $totalSpentThisMonth = 0;
        $totalSessionsCompleted = 0;

        if (!$isTrainer) {
            $totalSpentThisMonth = Booking::where('trainee_id', $userId)
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('session_date', '>=', now()->startOfMonth())
                ->where('session_date', '<=', now()->endOfMonth())
                ->sum('amount');
                
            $totalSessionsCompleted = Booking::where('trainee_id', $userId)
                ->where('status', 'completed')
                ->count();
        }

        return view('bookings.index', compact(
            'upcomingBookings', 'pastBookings', 'cancelledBookings',
            'totalSpentThisMonth', 'totalSessionsCompleted', 'isTrainer'
        ));
    }
    
    /**
     * Update booking status
     * PUT /bookings/{id}
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            abort(403);
        }
        
        $booking = Booking::where(function($query) {
            $query->where('trainer_id', Auth::id())
                  ->orWhere('trainee_id', Auth::id());
        })->findOrFail($id);

        $isTrainer = Auth::id() == $booking->trainer_id;
        $isTrainee = Auth::id() == $booking->trainee_id;
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:confirmed,completed,cancelled',
            'cancellation_reason' => 'required_if:status,cancelled|nullable|string|min:5|max:500',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('error', 'This booking is already cancelled.');
        }

        if ($request->status === 'completed' && !$isTrainer) {
            abort(403, 'Only trainers can mark sessions as completed.');
        }

        if ($request->status === 'confirmed') {
            abort(403, 'Bookings cannot be restored from this screen.');
        }
        
        $updateData = ['status' => $request->status];
        $message = 'Booking status updated!';
        
        if ($request->status == 'completed') {
            $updateData['completed_at'] = now();
        } elseif ($request->status == 'cancelled') {
            $updateData['cancelled_at'] = now();
            $updateData['cancelled_by'] = Auth::id();
            $updateData['cancellation_reason'] = $request->cancellation_reason;
            $updateData['cancellation_policy'] = $isTrainer ? 'trainer_refund' : 'trainee_no_refund';

            if ($isTrainer) {
                $refundData = $this->createTrainerCancellationRefundRequest($booking);
                $updateData = array_merge($updateData, $refundData);
                $message = 'Session cancelled. A refund request has been sent to admin for the trainee.';
            } elseif ($isTrainee) {
                $updateData['refund_status'] = 'not_applicable';
                $updateData['refund_amount'] = 0;
                $message = 'Session cancelled. Trainee cancellations are not eligible for a refund.';
            }
        }

        $booking->update($updateData);
        
        return redirect()->back()->with('success', $message);
    }

    private function createTrainerCancellationRefundRequest(Booking $booking): array
    {
        $booking->loadMissing('trainee');

        $amount = (float) ($booking->amount ?? 0);
        $upiId = $booking->trainee->upi_id ?? null;
        $payment = Payment::where('booking_id', $booking->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'refund_pending',
                'refund_amount' => $amount,
                'refund_error' => $upiId ? null : 'Trainee UPI ID is missing.',
            ]);
        }

        return [
            'refund_status' => 'pending_admin',
            'refund_amount' => $amount,
            'refund_upi_id' => $upiId,
            'refund_requested_at' => now(),
            'refund_processed_at' => null,
            'refund_reference' => null,
            'refund_error' => $upiId ? null : 'Trainee UPI ID is missing.',
        ];
    }
}
