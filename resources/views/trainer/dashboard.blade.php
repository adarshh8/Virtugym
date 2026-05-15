@extends('layouts.app')

@section('title', 'Trainer Dashboard')

@section('content')
<div style="max-width:1280px;margin:0 auto;">

    {{-- Welcome Banner --}}
    <div style="background:linear-gradient(135deg,rgba(139,92,246,.18),rgba(236,72,153,.12));border:1px solid rgba(139,92,246,.28);border-radius:24px;padding:2.2rem 2.5rem;margin-bottom:2rem;position:relative;overflow:hidden;" class="fade-in-up">
        <div style="position:absolute;inset:0;background:conic-gradient(from 0deg at 100% 0%,rgba(139,92,246,.08) 0deg,transparent 80deg);pointer-events:none;"></div>
        <div style="position:relative;z-index:1;">
            <h1 style="font-size:clamp(1.5rem,3vw,2.2rem);font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd 60%,#f9a8d4 90%);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
                Welcome back, {{ auth()->user()->name }}! 🏋️
            </h1>
            <p style="color:rgba(255,255,255,.38);font-size:.92rem;">Manage your clients and training sessions</p>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1.2rem;margin-bottom:2rem;">
        <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;transition:all .35s;" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform=''">
            <p style="color:rgba(255,255,255,.35);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;">TOTAL CLIENTS</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $stats['total_clients'] ?? 0 }}</p>
        </div>
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;transition:all .35s;" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform=''">
            <p style="color:rgba(255,255,255,.35);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;">TOTAL SESSIONS</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#6ee7b7,#34d399);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $stats['total_sessions'] ?? 0 }}</p>
        </div>
        <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;transition:all .35s;" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform=''">
            <p style="color:rgba(255,255,255,.35);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;">TOTAL EARNED</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#fbbf24,#f59e0b);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">₹{{ number_format($stats['total_earned'] ?? 0) }}</p>
        </div>
        <div class="fade-in-up delay-4" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;transition:all .35s;" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform=''">
            <p style="color:rgba(255,255,255,.35);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;">RATING</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#fb923c,#f97316);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $stats['rating'] ?? 0 }} <span style="font-size:1.2rem;">⭐</span></p>
        </div>
    </div>

    <div style="margin-bottom:1.5rem;" class="fade-in-up delay-1">
        <a href="{{ route('bookings.index') }}" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;padding:11px 24px;border-radius:12px;font-size:.88rem;font-weight:700;text-decoration:none;box-shadow:0 8px 20px rgba(139,92,246,.35);transition:all .3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 14px 30px rgba(139,92,246,.5)'" onmouseout="this.style.transform='';this.style.boxShadow='0 8px 20px rgba(139,92,246,.35)'">
            📅 View All Bookings →
        </a>
    </div>

    {{-- Sessions & Workouts --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.5rem;">

        {{-- Upcoming Sessions --}}
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;margin-bottom:1.2rem;">📅 Upcoming Sessions</h2>
            @if(isset($upcomingBookings) && $upcomingBookings->count() > 0)
                <div style="display:flex;flex-direction:column;gap:.8rem;">
                    @foreach($upcomingBookings as $booking)
                        @php
                            $sessionDate = \Carbon\Carbon::parse($booking->session_date);
                            $joinAt = $sessionDate->copy()->subMinutes(10);
                            $canJoin = now()->greaterThanOrEqualTo($joinAt);
                        @endphp
                        <div style="border-bottom:1px solid rgba(139,92,246,.1);padding-bottom:.8rem;position:relative;">
                            <p style="font-size:.88rem;font-weight:600;color:#e2d9f3;margin-bottom:2px;">{{ $booking->trainee->name ?? 'Trainee' }}</p>
                            <p style="font-size:.75rem;color:rgba(255,255,255,.3);">{{ $sessionDate->format('M d, Y h:i A') }}</p>
                            @if($canJoin)
                                <a href="{{ route('video-call.join', $booking->id) }}" style="font-size:.7rem;color:#6ee7b7;font-weight:700;position:absolute;bottom:0.8rem;right:0;text-decoration:none;">Join Session</a>
                            @else
                                <span style="font-size:.7rem;color:#fca5a5;font-weight:600;position:absolute;bottom:0.8rem;right:0;">Opens {{ $joinAt->format('h:i A') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:2rem 1rem;">
                    <div style="font-size:2rem;margin-bottom:.5rem;opacity:.4;">📅</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.85rem;">No upcoming sessions</p>
                </div>
            @endif
        </div>

        {{-- My Workouts --}}
        <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;margin-bottom:1.2rem;">🏋️ My Workouts</h2>
            @if(isset($myWorkouts) && $myWorkouts->count() > 0)
                <div style="display:flex;flex-direction:column;gap:.8rem;">
                    @foreach($myWorkouts as $workout)
                        <div style="border-bottom:1px solid rgba(139,92,246,.1);padding-bottom:.8rem;">
                            <p style="font-size:.88rem;font-weight:600;color:#e2d9f3;margin-bottom:2px;">{{ $workout->title }}</p>
                            <p style="font-size:.75rem;color:rgba(255,255,255,.3);">{{ $workout->type }} • {{ $workout->difficulty }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:2rem 1rem;">
                    <div style="font-size:2rem;margin-bottom:.5rem;opacity:.4;">🏋️</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.85rem;">No workouts created yet</p>
                    <a href="#" style="display:inline-block;margin-top:.6rem;font-size:.8rem;color:#a78bfa;font-weight:600;text-decoration:none;">+ Create Workout (Coming Soon)</a>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
