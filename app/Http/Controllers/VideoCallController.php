<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoCallController extends Controller
{
    public function join($booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        
        // Check if user is part of this booking
        if (Auth::id() != $booking->trainee_id && Auth::id() != $booking->trainer_id) {
            abort(403);
        }

        if ($booking->status !== 'confirmed') {
            return redirect()->route('bookings.index')->with('error', 'Only confirmed sessions can be joined.');
        }
        
        // Check if session time has arrived (10 minutes before allowed)
        $sessionTime = strtotime($booking->session_date);
        $now = time();
        $joinOpensAt = $sessionTime - 600;
        $canJoin = ($now >= $joinOpensAt);
        
        if (!$canJoin) {
            $waitMinutes = ceil(($joinOpensAt - $now) / 60);
            return redirect()->back()->with('error', "Video session opens 10 minutes before the scheduled time. Please try again in {$waitMinutes} minutes.");
        }
        
        // Generate Jitsi meeting link
        $meetingId = $booking->meeting_id ?? 'virtugym_' . $booking->id . '_' . md5($booking->id);
        $meetingLink = "https://meet.jit.si/" . $meetingId;
        
        // Update booking with meeting details
        if (!$booking->meeting_id) {
            $booking->update([
                'meeting_id' => $meetingId,
                'meeting_link' => $meetingLink
            ]);
        }
        
        return view('video-call.join', compact('booking', 'meetingLink', 'meetingId'));
    }
    
    public function startMeeting($booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        
        if (Auth::id() != $booking->trainer_id) {
            abort(403);
        }
        
        $booking->update(['meeting_started' => true]);
        
        return redirect()->route('video-call.join', $booking_id);
    }
    
    public function endMeeting($booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        
        if (Auth::id() != $booking->trainer_id) {
            abort(403);
        }
        
        $booking->update(['meeting_ended' => true]);
        
        return redirect()->route('bookings.index')->with('success', 'Session completed!');
    }
}
