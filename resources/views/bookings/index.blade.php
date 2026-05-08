@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">My Bookings 📅</h1>
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if(isset($bookings) && $bookings->count() > 0)
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex flex-wrap justify-between items-start">
                        <div>
                            @if(Auth::user()->role == 'trainer')
                                <h3 class="text-xl font-bold text-purple-600">{{ $booking->trainee->name ?? 'Trainee' }}</h3>
                                <p class="text-sm text-gray-500">Trainee</p>
                            @else
                                <h3 class="text-xl font-bold text-purple-600">{{ $booking->trainer->name ?? 'Trainer' }}</h3>
                                <p class="text-sm text-gray-500">{{ $booking->trainer->specialization ?? 'Personal Trainer' }}</p>
                            @endif
                            
                            <p class="text-gray-600 mt-2">
                                📅 {{ \Carbon\Carbon::parse($booking->session_date)->format('F d, Y') }} at 
                                ⏰ {{ \Carbon\Carbon::parse($booking->session_date)->format('h:i A') }}
                            </p>
                            <p class="text-gray-600">
                                ⏱️ {{ $booking->duration_minutes }} minutes • 
                                💰 ₹{{ number_format($booking->amount) }}
                            </p>
                            @if($booking->special_requests)
                                <p class="text-gray-500 text-sm mt-1">📝 {{ $booking->special_requests }}</p>
                            @endif
                        </div>
                        
                        <div class="text-right">
                            @php
                                $sessionTime = strtotime($booking->session_date);
                                $now = time();
                                $canJoin = ($now >= $sessionTime - 900); // 15 minutes before
                                $isUpcoming = ($sessionTime > $now);
                            @endphp
                            
                            @if($booking->status == 'confirmed')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">✓ Confirmed</span>
                                
                                <!-- Video Call Button - Shows for BOTH Trainer and Trainee -->
                                @if($canJoin)
                                    <a href="{{ route('video-call.join', $booking->id) }}" 
                                       class="mt-2 inline-block bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition">
                                        🎥 Join Video Session
                                    </a>
                                @elseif($isUpcoming)
                                    <p class="text-xs text-gray-400 mt-2">
                                        ⏰ Session available {{ ceil(($sessionTime - 900 - $now) / 60) }} minutes before scheduled time
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-2">
                                        ⏰ Session time has passed
                                    </p>
                                @endif

                                @if(Auth::user()->role == 'trainee')
                                    <a href="{{ route('music.index') }}"
                                       class="mt-2 inline-block bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-700 transition">
                                        🎵 Workout Music
                                    </a>
                                @endif
                                
                                <!-- Trainer Status Update -->
                                @if(Auth::user()->role == 'trainer')
                                    <form method="POST" action="{{ route('bookings.update', $booking->id) }}" class="mt-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="border rounded-lg px-2 py-1 text-sm">
                                            <option value="confirmed" selected>Confirmed</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </form>
                                @endif
                                
                            @elseif($booking->status == 'completed')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">✓ Completed</span>
                            @elseif($booking->status == 'cancelled')
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">✗ Cancelled</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $bookings->links() }}</div>
    @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <div class="text-6xl mb-4">📅</div>
            <h3 class="text-xl font-bold text-gray-800">No bookings yet</h3>
            <p class="text-gray-600 mt-2">
                @if(Auth::user()->role == 'trainer')
                    Wait for trainees to book your sessions
                @else
                    Book a trainer to start your fitness journey
                @endif
            </p>
            @if(Auth::user()->role != 'trainer')
                <a href="{{ route('trainee.trainers') }}" class="inline-block mt-4 bg-purple-600 text-white px-6 py-2 rounded-lg">Browse Trainers</a>
            @endif
        </div>
    @endif
</div>
@endsection
