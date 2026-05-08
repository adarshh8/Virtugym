@extends('layouts.app')

@section('title', 'Trainee Dashboard')

@section('content')
<div style="max-width:1280px;margin:0 auto;">

    {{-- Welcome Banner --}}
    <div style="background:linear-gradient(135deg,rgba(139,92,246,.18),rgba(236,72,153,.12));border:1px solid rgba(139,92,246,.28);border-radius:24px;padding:2.2rem 2.5rem;margin-bottom:2rem;position:relative;overflow:hidden;" class="fade-in-up">
        <div style="position:absolute;inset:0;background:conic-gradient(from 0deg at 100% 0%,rgba(139,92,246,.08) 0deg,transparent 80deg);pointer-events:none;"></div>
        <div style="position:relative;z-index:1;">
            <h1 style="font-size:clamp(1.5rem,3vw,2.2rem);font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd 60%,#f9a8d4 90%);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
                Welcome back, {{ auth()->user()->name }}! 💪
            </h1>
            <p style="color:rgba(255,255,255,.38);font-size:.92rem;">Track your fitness journey</p>
        </div>
    </div>

    <x-activity-calendar :calendar="$activityCalendar ?? collect()" :total="$activityTotal ?? 0" :streak="$streak ?? 0" />

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1.2rem;margin-bottom:2rem;">
        <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;transition:all .35s;" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform=''">
            <p style="color:rgba(255,255,255,.35);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;">TOTAL WORKOUTS</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $stats['total_workouts'] ?? 0 }}</p>
        </div>
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;transition:all .35s;" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform=''">
            <p style="color:rgba(255,255,255,.35);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;">COMPLETED</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#6ee7b7,#34d399);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $stats['completed_workouts'] ?? 0 }}</p>
        </div>
        <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;transition:all .35s;" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform=''">
            <p style="color:rgba(255,255,255,.35);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;">TOTAL BOOKINGS</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#93c5fd,#60a5fa);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $stats['total_bookings'] ?? 0 }}</p>
        </div>
        <div class="fade-in-up delay-4" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;transition:all .35s;" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform=''">
            <p style="color:rgba(255,255,255,.35);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;">UPCOMING SESSIONS</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#fb923c,#f97316);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $stats['upcoming_sessions'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Recent Workouts & Upcoming Sessions --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.5rem;margin-bottom:2rem;">

        {{-- Recent Workouts --}}
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;margin-bottom:1.2rem;">🏋️ Recent Workouts</h2>
            @if(isset($recentWorkouts) && $recentWorkouts->count() > 0)
                <div style="display:flex;flex-direction:column;gap:.8rem;">
                    @foreach($recentWorkouts as $workout)
                        <div style="border-bottom:1px solid rgba(139,92,246,.1);padding-bottom:.8rem;">
                            <p style="font-size:.88rem;font-weight:600;color:#e2d9f3;margin-bottom:2px;">{{ $workout->title }}</p>
                            <p style="font-size:.75rem;color:rgba(255,255,255,.3);">{{ $workout->type }} • {{ $workout->difficulty }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:2rem 1rem;">
                    <div style="font-size:2rem;margin-bottom:.5rem;opacity:.4;">🏋️</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.85rem;">No workouts yet</p>
                    <a href="#" style="display:inline-block;margin-top:.6rem;font-size:.8rem;color:#a78bfa;font-weight:600;text-decoration:none;">+ Create Workout (Coming Soon)</a>
                </div>
            @endif
        </div>

        {{-- Upcoming Sessions --}}
        <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;margin-bottom:1.2rem;">📅 Upcoming Sessions</h2>
            @if(isset($upcomingSessions) && $upcomingSessions->count() > 0)
                <div style="display:flex;flex-direction:column;gap:.8rem;">
                    @foreach($upcomingSessions as $session)
                        <div style="border-bottom:1px solid rgba(139,92,246,.1);padding-bottom:.8rem;">
                            <p style="font-size:.88rem;font-weight:600;color:#e2d9f3;margin-bottom:2px;">{{ $session->trainer->name ?? 'Trainer' }}</p>
                            <p style="font-size:.75rem;color:rgba(255,255,255,.3);">{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y h:i A') }}</p>
                            <span style="font-size:.7rem;background:rgba(139,92,246,.15);color:#a78bfa;border:1px solid rgba(139,92,246,.25);padding:2px 8px;border-radius:50px;font-weight:600;">{{ ucfirst($session->status) }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('bookings.index') }}" style="display:inline-block;margin-top:1rem;font-size:.8rem;color:#a78bfa;font-weight:600;text-decoration:none;">View All Bookings →</a>
            @else
                <div style="text-align:center;padding:2rem 1rem;">
                    <div style="font-size:2rem;margin-bottom:.5rem;opacity:.4;">📅</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.85rem;">No upcoming sessions</p>
                    <a href="{{ route('trainee.trainers') }}" style="display:inline-block;margin-top:.6rem;font-size:.8rem;color:#a78bfa;font-weight:600;text-decoration:none;">+ Browse Trainers</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Available Trainers --}}
    <div class="fade-in-up delay-4" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.4rem;">
            <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;">🏆 Available Trainers</h2>
            <a href="{{ route('trainee.trainers') }}" style="font-size:.78rem;color:#a78bfa;font-weight:600;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#c4b5fd'" onmouseout="this.style.color='#a78bfa'">View All →</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;">
            @if(isset($availableTrainers) && $availableTrainers->count() > 0)
                @foreach($availableTrainers as $trainer)
                    <div style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.18);border-radius:16px;padding:1.2rem;text-align:center;transition:all .3s;" onmouseover="this.style.borderColor='rgba(139,92,246,.4)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.18)';this.style.transform=''">
                        <div style="font-size:2.2rem;margin-bottom:.5rem;">🏋️</div>
                        <h3 style="font-size:.9rem;font-weight:700;color:#e2d9f3;margin-bottom:2px;">{{ $trainer->name }}</h3>
                        <p style="font-size:.75rem;color:rgba(255,255,255,.3);margin-bottom:4px;">{{ $trainer->specialization ?? 'Personal Trainer' }}</p>
                        <p style="font-size:.82rem;font-weight:700;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:2px;">₹{{ $trainer->hourly_rate ?? 500 }}/hr</p>
                        <p style="font-size:.72rem;color:rgba(255,255,255,.25);margin-bottom:.9rem;">⭐ {{ $trainer->rating ?? '4.8' }} ({{ $trainer->total_clients ?? 0 }} clients)</p>
                        <div style="display:flex;gap:.5rem;">
                            <a href="{{ route('chat.index', $trainer->id) }}" style="flex:1;text-align:center;background:rgba(255,255,255,.06);border:1px solid rgba(139,92,246,.2);color:rgba(196,181,253,.7);padding:7px 8px;border-radius:10px;font-size:.75rem;font-weight:600;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='rgba(139,92,246,.15)';this.style.color='#c4b5fd'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.color='rgba(196,181,253,.7)'">💬 Chat</a>
                            <a href="{{ route('book.trainer.create', $trainer->id) }}" style="flex:1;text-align:center;background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;padding:7px 8px;border-radius:10px;font-size:.75rem;font-weight:700;text-decoration:none;box-shadow:0 4px 12px rgba(139,92,246,.3);transition:all .2s;" onmouseover="this.style.boxShadow='0 8px 20px rgba(139,92,246,.5)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(139,92,246,.3)'">Book Now</a>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="grid-column:1/-1;text-align:center;padding:2.5rem;">
                    <div style="font-size:2rem;margin-bottom:.5rem;opacity:.4;">🏋️</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.88rem;">No trainers available at the moment</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
