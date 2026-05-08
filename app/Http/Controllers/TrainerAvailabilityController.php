<?php

namespace App\Http\Controllers;

use App\Models\TrainerAvailability;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrainerAvailabilityController extends Controller
{
    public function index()
    {
        $trainer = Auth::user();
        $availabilities = TrainerAvailability::where('trainer_id', $trainer->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
            
        return view('trainer.availability', compact('availabilities'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $availability = TrainerAvailability::create([
            'trainer_id' => Auth::id(),
            'day_of_week' => (int)$request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_recurring' => false,
            'is_booked' => false
        ]);
        
        return redirect()->back()->with('success', 'Availability slot added successfully!');
    }
    
    public function destroy($id)
    {
        $availability = TrainerAvailability::where('trainer_id', Auth::id())->findOrFail($id);
        $availability->delete();
        
        return redirect()->back()->with('success', 'Availability slot removed!');
    }
    
    public function getAvailableSlots($trainer_id, $date)
    {
        $dayOfWeek = (int)date('w', strtotime($date));
        
        // Debug
        \Log::info('Searching for slots - Trainer: ' . $trainer_id . ', Day: ' . $dayOfWeek);
        
        $slots = TrainerAvailability::where('trainer_id', $trainer_id)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        $bookedTimes = Booking::where('trainer_id', $trainer_id)
            ->whereDate('session_date', $date)
            ->where('status', 'confirmed')
            ->get()
            ->map(function ($booking) {
                return date('H:i', strtotime($booking->session_date));
            })
            ->all();
        
        \Log::info('Found ' . $slots->count() . ' slots');
        
        $availableSlots = [];
        foreach ($slots as $slot) {
            $startTime = date('H:i', strtotime($slot->start_time));

            if (in_array($startTime, $bookedTimes, true)) {
                continue;
            }

            $availableSlots[] = [
                'id' => (string)$slot->_id,
                'start_time' => $startTime,
                'end_time' => date('H:i', strtotime($slot->end_time))
            ];
        }
        
        return response()->json($availableSlots);
    }
}
