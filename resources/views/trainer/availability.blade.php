@extends('layouts.app')

@section('title', 'Manage Availability')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Manage Your Availability ⏰</h1>
    <p class="text-gray-600 mb-8">Set your available time slots. Trainees can only book during these times.</p>
    
    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Add Availability Form -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">➕ Add Time Slot</h2>
            
            <form method="POST" action="{{ route('trainer.availability.store') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">📅 Day of Week</label>
                    <select name="day_of_week" class="w-full px-4 py-2 border rounded-lg" required>
                        <option value="0">Sunday</option>
                        <option value="1">Monday</option>
                        <option value="2">Tuesday</option>
                        <option value="3">Wednesday</option>
                        <option value="4">Thursday</option>
                        <option value="5">Friday</option>
                        <option value="6">Saturday</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">🕐 Start Time</label>
                        <input type="time" name="start_time" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">🕒 End Time</label>
                        <input type="time" name="end_time" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-2 rounded-lg font-semibold hover:shadow-lg transition">
                    + Add Slot
                </button>
            </form>
        </div>
        
        <!-- Current Availability List -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">📋 Your Available Slots</h2>
            
            @if($availabilities->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($availabilities as $slot)
                        <div class="flex justify-between items-center border-b pb-3">
                            <div>
                                <p class="font-semibold">
                                    {{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$slot->day_of_week] }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    🕐 {{ date('h:i A', strtotime($slot->start_time)) }} - 
                                    {{ date('h:i A', strtotime($slot->end_time)) }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('trainer.availability.destroy', $slot->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                    🗑️ Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-5xl mb-3">⏰</div>
                    <p class="text-gray-500">No availability slots added yet.</p>
                    <p class="text-sm text-gray-400 mt-2">Add your first time slot to start receiving bookings!</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Info Box -->
    <div class="mt-8 bg-blue-50 rounded-xl p-4">
        <div class="flex items-start space-x-3">
            <span class="text-2xl">💡</span>
            <div>
                <h3 class="font-semibold text-blue-800">Pro Tip</h3>
                <p class="text-sm text-blue-700">A trainee can book only one session for a specific date and time. The same weekday slot remains available on other dates.</p>
            </div>
        </div>
    </div>
</div>
@endsection
