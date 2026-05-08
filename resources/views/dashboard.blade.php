@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="max-width:1280px;margin:0 auto;">

    {{-- Welcome Banner --}}
    <div style="background:linear-gradient(135deg,rgba(139,92,246,.18),rgba(236,72,153,.12));border:1px solid rgba(139,92,246,.28);border-radius:24px;padding:2.2rem 2.5rem;margin-bottom:2rem;position:relative;overflow:hidden;" class="fade-in-up">
        <div style="position:absolute;inset:0;background:conic-gradient(from 0deg at 100% 0%,rgba(139,92,246,.08) 0deg,transparent 80deg);pointer-events:none;"></div>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;position:relative;z-index:1;">
            <div>
                <h1 style="font-size:clamp(1.5rem,3vw,2.2rem);font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd 60%,#f9a8d4 90%);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
                    Welcome back, {{ auth()->user()->name }}! 💪
                </h1>
                <p style="color:rgba(255,255,255,.38);font-size:.92rem;">Ready to crush your fitness goals today?</p>
            </div>
            <div style="background:rgba(255,255,255,.06);border:1px solid rgba(139,92,246,.25);border-radius:16px;padding:18px 28px;text-align:center;min-width:110px;">
                <div style="font-size:2rem;font-weight:900;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;">{{ $streak ?? 0 }}</div>
                <div style="font-size:.75rem;color:rgba(255,255,255,.35);margin-top:2px;">Day Streak 🔥</div>
            </div>
        </div>
    </div>

    <x-activity-calendar :calendar="$activityCalendar ?? collect()" :total="$activityTotal ?? 0" :streak="$streak ?? 0" />

    {{-- Stats Cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.2rem;margin-bottom:2rem;">
        {{-- Total Workouts --}}
        <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;display:flex;align-items:center;justify-content:space-between;transition:all .35s cubic-bezier(.23,1,.32,1);" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 36px rgba(0,0,0,.3)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform='';this.style.boxShadow=''">
            <div>
                <p style="color:rgba(255,255,255,.35);font-size:.75rem;font-weight:600;letter-spacing:.04em;margin-bottom:.3rem;">TOTAL WORKOUTS</p>
                <p style="font-size:2.2rem;font-weight:900;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $totalWorkouts ?? 0 }}</p>
                <p style="font-size:.73rem;color:rgba(16,185,129,.7);margin-top:4px;">Completed: {{ $completedWorkouts ?? 0 }}</p>
            </div>
            <div style="font-size:2.2rem;opacity:.7;">🏋️</div>
        </div>

        {{-- Exercises --}}
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;display:flex;align-items:center;justify-content:space-between;transition:all .35s cubic-bezier(.23,1,.32,1);" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 36px rgba(0,0,0,.3)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform='';this.style.boxShadow=''">
            <div>
                <p style="color:rgba(255,255,255,.35);font-size:.75rem;font-weight:600;letter-spacing:.04em;margin-bottom:.3rem;">EXERCISES LOGGED</p>
                <p style="font-size:2.2rem;font-weight:900;background:linear-gradient(135deg,#6ee7b7,#34d399);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $totalExercisesLogged ?? 0 }}</p>
                <p style="font-size:.73rem;color:rgba(255,255,255,.3);margin-top:4px;">Volume: {{ number_format($totalVolume ?? 0) }} kg</p>
            </div>
            <div style="font-size:2.2rem;opacity:.7;">📊</div>
        </div>

        {{-- Weight --}}
        <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;display:flex;align-items:center;justify-content:space-between;transition:all .35s cubic-bezier(.23,1,.32,1);" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 36px rgba(0,0,0,.3)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform='';this.style.boxShadow=''">
            <div>
                <p style="color:rgba(255,255,255,.35);font-size:.75rem;font-weight:600;letter-spacing:.04em;margin-bottom:.3rem;">CURRENT WEIGHT</p>
                <p style="font-size:2.2rem;font-weight:900;background:linear-gradient(135deg,#fbbf24,#f59e0b);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">
                    {{ $latestProgress->weight ?? '—' }}<span style="font-size:1rem;font-weight:600;"> kg</span>
                </p>
                <p style="font-size:.73rem;color:rgba(255,255,255,.3);margin-top:4px;">
                    {{ $latestProgress ? $latestProgress->date->format('M d, Y') : 'Not tracked' }}
                </p>
            </div>
            <div style="font-size:2.2rem;opacity:.7;">⚖️</div>
        </div>

        {{-- Fitness Level --}}
        <div class="fade-in-up delay-4" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;display:flex;align-items:center;justify-content:space-between;transition:all .35s cubic-bezier(.23,1,.32,1);" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 36px rgba(0,0,0,.3)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform='';this.style.boxShadow=''">
            <div>
                <p style="color:rgba(255,255,255,.35);font-size:.75rem;font-weight:600;letter-spacing:.04em;margin-bottom:.3rem;">FITNESS LEVEL</p>
                <p style="font-size:2.2rem;font-weight:900;background:linear-gradient(135deg,#f9a8d4,#ec4899);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;text-transform:capitalize;">
                    {{ auth()->user()->fitness_level ?? 'Beginner' }}
                </p>
                <p style="font-size:.73rem;color:rgba(255,255,255,.3);margin-top:4px;text-transform:capitalize;">Goal: {{ auth()->user()->goal ?? 'Fitness' }}</p>
            </div>
            <div style="font-size:2.2rem;opacity:.7;">🎯</div>
        </div>
    </div>

    {{-- Recent Workouts & Personal Records --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.5rem;margin-bottom:2rem;">

        {{-- Recent Workouts --}}
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.2rem;">
                <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;">Recent Workouts</h2>
                <a href="{{ route('workouts.index') }}" style="font-size:.78rem;color:#a78bfa;font-weight:600;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#c4b5fd'" onmouseout="this.style.color='#a78bfa'">View All →</a>
            </div>

            @if(isset($recentWorkouts) && $recentWorkouts->count() > 0)
                <div style="display:flex;flex-direction:column;gap:.8rem;">
                    @foreach($recentWorkouts as $workout)
                        <div style="border-bottom:1px solid rgba(139,92,246,.1);padding-bottom:.8rem;display:flex;justify-content:space-between;align-items:flex-start;" class="{{ $loop->last ? 'no-border' : '' }}">
                            <div>
                                <h3 style="font-size:.88rem;font-weight:600;color:#e2d9f3;margin-bottom:2px;">{{ $workout->title }}</h3>
                                <p style="font-size:.75rem;color:rgba(255,255,255,.3);">{{ $workout->type ?? 'Workout' }} • {{ $workout->duration_minutes ?? 'N/A' }} mins</p>
                            </div>
                            <div style="text-align:right;flex-shrink:0;margin-left:1rem;">
                                <p style="font-size:.72rem;color:rgba(255,255,255,.25);">{{ $workout->created_at->diffForHumans() }}</p>
                                @if($workout->completed_at)
                                    <span style="font-size:.7rem;color:#6ee7b7;font-weight:600;">✓ Done</span>
                                @else
                                    <span style="font-size:.7rem;color:#fbbf24;font-weight:600;">Pending</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:2.5rem 1rem;">
                    <div style="font-size:2.5rem;margin-bottom:.5rem;opacity:.5;">🏋️</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.85rem;">No workouts yet</p>
                    <a href="#" style="display:inline-block;margin-top:.8rem;font-size:.8rem;color:#a78bfa;font-weight:600;text-decoration:none;">Create your first workout →</a>
                </div>
            @endif
        </div>

        {{-- Personal Records --}}
        <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.2rem;">
                <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;">🏆 Personal Records</h2>
                <a href="{{ route('progress.index') }}" style="font-size:.78rem;color:#a78bfa;font-weight:600;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#c4b5fd'" onmouseout="this.style.color='#a78bfa'">View All →</a>
            </div>

            @if(isset($prs) && $prs->count() > 0)
                <div style="display:flex;flex-direction:column;gap:.8rem;">
                    @foreach($prs as $pr)
                        <div style="border-bottom:1px solid rgba(139,92,246,.1);padding-bottom:.8rem;display:flex;justify-content:space-between;align-items:flex-start;">
                            <div>
                                <h3 style="font-size:.88rem;font-weight:600;color:#e2d9f3;margin-bottom:2px;">{{ $pr->exercise_name }}</h3>
                                <p style="font-size:.75rem;color:rgba(255,255,255,.3);">
                                    {{ $pr->weight }} kg × {{ is_array($pr->reps) ? implode(', ', $pr->reps) : $pr->reps }} reps
                                </p>
                            </div>
                            <div style="text-align:right;flex-shrink:0;margin-left:1rem;">
                                <span style="font-size:.68rem;background:rgba(251,191,36,.15);color:#fbbf24;border:1px solid rgba(251,191,36,.3);padding:2px 8px;border-radius:50px;font-weight:700;">NEW PR!</span>
                                <p style="font-size:.72rem;color:rgba(255,255,255,.25);margin-top:3px;">{{ $pr->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:2.5rem 1rem;">
                    <div style="font-size:2.5rem;margin-bottom:.5rem;opacity:.5;">🏆</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.85rem;">No personal records yet</p>
                    <p style="font-size:.78rem;color:rgba(255,255,255,.2);margin-top:4px;">Complete workouts to set PRs!</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.2rem;" class="fade-in-up delay-4">

        <a href="{{ route('analytics.index') }}" style="background:linear-gradient(135deg,rgba(59,130,246,.18),rgba(37,99,235,.12));border:1px solid rgba(59,130,246,.28);border-radius:20px;padding:1.6rem;text-align:center;text-decoration:none;transition:all .3s cubic-bezier(.23,1,.32,1);display:block;" onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 20px 40px rgba(59,130,246,.2)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="font-size:2rem;margin-bottom:.6rem;">📊</div>
            <h3 style="font-size:.95rem;font-weight:700;color:#93c5fd;margin-bottom:.3rem;">Analytics</h3>
            <p style="font-size:.78rem;color:rgba(255,255,255,.3);">View performance charts</p>
        </a>

        <a href="{{ route('chat.index') }}" style="background:linear-gradient(135deg,rgba(16,185,129,.18),rgba(5,150,105,.12));border:1px solid rgba(16,185,129,.28);border-radius:20px;padding:1.6rem;text-align:center;text-decoration:none;transition:all .3s cubic-bezier(.23,1,.32,1);display:block;" onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 20px 40px rgba(16,185,129,.2)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="font-size:2rem;margin-bottom:.6rem;">💬</div>
            <h3 style="font-size:.95rem;font-weight:700;color:#6ee7b7;margin-bottom:.3rem;">Messages</h3>
            <p style="font-size:.78rem;color:rgba(255,255,255,.3);">Chat with your trainer</p>
        </a>

        <a href="{{ route('bookings.index') }}" style="background:linear-gradient(135deg,rgba(139,92,246,.18),rgba(236,72,153,.12));border:1px solid rgba(139,92,246,.28);border-radius:20px;padding:1.6rem;text-align:center;text-decoration:none;transition:all .3s cubic-bezier(.23,1,.32,1);display:block;" onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 20px 40px rgba(139,92,246,.2)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="font-size:2rem;margin-bottom:.6rem;">📅</div>
            <h3 style="font-size:.95rem;font-weight:700;color:#c4b5fd;margin-bottom:.3rem;">Bookings</h3>
            <p style="font-size:.78rem;color:rgba(255,255,255,.3);">Manage your sessions</p>
        </a>
    </div>

</div>
@endsection
