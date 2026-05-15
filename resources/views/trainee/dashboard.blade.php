@extends('layouts.app')

@section('title', 'Trainee Dashboard')

@section('content')
<div style="max-width:1280px;margin:0 auto;">

    {{-- Welcome Banner --}}
    <div style="background:linear-gradient(135deg,rgba(139,92,246,.18),rgba(236,72,153,.12));border:1px solid rgba(139,92,246,.28);border-radius:24px;padding:2.2rem 2.5rem;margin-bottom:1.5rem;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.5rem;" class="fade-in-up">
        <div style="position:absolute;inset:0;background:conic-gradient(from 0deg at 100% 0%,rgba(139,92,246,.08) 0deg,transparent 80deg);pointer-events:none;"></div>
        <div style="position:relative;z-index:1;display:flex;align-items:center;gap:1.2rem;">
            <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:bold;color:#fff;">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div>
                <h1 style="font-size:clamp(1.5rem,3vw,2.2rem);font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd 60%,#f9a8d4 90%);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
                    Welcome back, {{ explode(' ', auth()->user()->name)[0] }}! 💪
                </h1>
                <p style="color:rgba(255,255,255,.38);font-size:.92rem;">Track your fitness journey — you're doing great this week. Keep the momentum going!</p>
            </div>
        </div>
        <div style="position:relative;z-index:1;text-align:right;">
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);padding:10px 18px;border-radius:16px;">
                <span style="font-size:1.8rem;">🔥</span>
                <div style="text-align:left;">
                    <div style="font-size:1.6rem;font-weight:900;color:#fff;line-height:1;">{{ $streak ?? 0 }}</div>
                    <div style="font-size:.75rem;color:rgba(255,255,255,.5);font-weight:600;text-transform:uppercase;">Day Streak</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Motivational Quote --}}
    <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:2.5rem 2rem;margin-bottom:2rem;text-align:center;">
        <p style="font-size:1.1rem;color:rgba(255,255,255,.8);font-style:italic;font-weight:500;">"{{ $motivationalQuote ?? 'Consistency is the key to progress.' }}"</p>
    </div>

    <x-activity-calendar :calendar="$activityCalendar ?? collect()" :total="$activityTotal ?? 0" :streak="$streak ?? 0" />

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1.2rem;margin-bottom:2rem;">
        @if(($stats['total_workouts'] ?? 0) > 0)
        <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;transition:all .35s;" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform=''">
            <p style="color:rgba(255,255,255,.35);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;">TOTAL WORKOUTS</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $stats['total_workouts'] }}</p>
        </div>
        @endif
        
        @if(($stats['completed_workouts'] ?? 0) > 0)
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.2);border-radius:20px;padding:1.5rem;transition:all .35s;" onmouseover="this.style.borderColor='rgba(139,92,246,.5)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.2)';this.style.transform=''">
            <p style="color:rgba(255,255,255,.35);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;">COMPLETED</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#6ee7b7,#34d399);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $stats['completed_workouts'] }}</p>
        </div>
        @endif
        

    </div>

    {{-- New Cards: Goals, BMI, AI Coach --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.5rem;margin-bottom:2rem;">
        
        {{-- Goal Progress Bars --}}
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;margin-bottom:1.2rem;">🎯 Goal Progress</h2>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:.8rem;color:rgba(255,255,255,.6);margin-bottom:.3rem;"><span>Workouts</span> <span style="font-weight:700;color:#e2d9f3;">4/5</span></div>
                    <div style="width:100%;height:6px;background:rgba(255,255,255,.1);border-radius:3px;overflow:hidden;"><div style="width:80%;height:100%;background:#8b5cf6;"></div></div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:.8rem;color:rgba(255,255,255,.6);margin-bottom:.3rem;"><span>Calories</span> <span style="font-weight:700;color:#e2d9f3;">72%</span></div>
                    <div style="width:100%;height:6px;background:rgba(255,255,255,.1);border-radius:3px;overflow:hidden;"><div style="width:72%;height:100%;background:#fb923c;"></div></div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:.8rem;color:rgba(255,255,255,.6);margin-bottom:.3rem;"><span>Hydration</span> <span style="font-weight:700;color:#e2d9f3;">90%</span></div>
                    <div style="width:100%;height:6px;background:rgba(255,255,255,.1);border-radius:3px;overflow:hidden;"><div style="width:90%;height:100%;background:#60a5fa;"></div></div>
                </div>
                <div style="margin-bottom:1rem;">
                    <div style="display:flex;justify-content:space-between;font-size:.8rem;color:rgba(255,255,255,.6);margin-bottom:.3rem;"><span>Sleep</span> <span style="font-weight:700;color:#e2d9f3;">85%</span></div>
                    <div style="width:100%;height:6px;background:rgba(255,255,255,.1);border-radius:3px;overflow:hidden;"><div style="width:85%;height:100%;background:#818cf8;"></div></div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:.8rem;color:rgba(255,255,255,.6);margin-bottom:.3rem;"><span>Strength</span> <span style="font-weight:700;color:#e2d9f3;">60%</span></div>
                    <div style="width:100%;height:6px;background:rgba(255,255,255,.1);border-radius:3px;overflow:hidden;"><div style="width:60%;height:100%;background:#f43f5e;"></div></div>
                </div>
            </div>
        </div>

        {{-- BMI / Body Stats --}}
        <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;margin-bottom:1.2rem;">⚖️ Body Stats</h2>
            @if(isset($latestProgress) && ($latestProgress->weight || $latestProgress->body_fat_percentage || $latestProgress->muscle_mass))
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                    <div style="text-align:center;flex:1;">
                        <div style="font-size:1.8rem;font-weight:900;color:#c4b5fd;line-height:1;margin-bottom:.2rem;">{{ $latestProgress->weight ?? '--' }}</div>
                        <div style="font-size:.75rem;color:rgba(255,255,255,.4);text-transform:uppercase;font-weight:600;">Weight (kg)</div>
                    </div>
                    <div style="width:1px;height:40px;background:rgba(255,255,255,.1);"></div>
                    <div style="text-align:center;flex:1;">
                        <div style="font-size:1.8rem;font-weight:900;color:#6ee7b7;line-height:1;margin-bottom:.2rem;">{{ $latestProgress->body_fat_percentage ?? '--' }}<span style="font-size:1rem">%</span></div>
                        <div style="font-size:.75rem;color:rgba(255,255,255,.4);text-transform:uppercase;font-weight:600;">Body Fat</div>
                    </div>
                    <div style="width:1px;height:40px;background:rgba(255,255,255,.1);"></div>
                    <div style="text-align:center;flex:1;">
                        <div style="font-size:1.8rem;font-weight:900;color:#f9a8d4;line-height:1;margin-bottom:.2rem;">{{ $latestProgress->muscle_mass ?? '--' }}<span style="font-size:1rem">%</span></div>
                        <div style="font-size:.75rem;color:rgba(255,255,255,.4);text-transform:uppercase;font-weight:600;">Muscle</div>
                    </div>
                </div>
                <a href="{{ route('progress.index') }}" style="display:block;text-align:center;font-size:.8rem;color:#a78bfa;font-weight:600;text-decoration:none;">Update Stats →</a>
            @else
                <div style="text-align:center;padding:1rem 0;">
                    <p style="color:rgba(255,255,255,.4);font-size:.85rem;margin-bottom:1rem;">No body stats recorded yet.</p>
                    <a href="{{ route('progress.index') }}" style="display:inline-block;background:rgba(139,92,246,.2);color:#c4b5fd;padding:8px 16px;border-radius:8px;font-size:.8rem;font-weight:600;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='rgba(139,92,246,.3)'" onmouseout="this.style.background='rgba(139,92,246,.2)'">+ Add Stats</a>
                </div>
            @endif
        </div>

        {{-- AI Coach --}}
        <div class="fade-in-up delay-4" style="background:linear-gradient(135deg,rgba(139,92,246,.1),rgba(139,92,246,.02));border:1px solid rgba(139,92,246,.3);border-radius:20px;padding:1.6rem;position:relative;min-height:140px;display:flex;flex-direction:column;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:1rem;">
                <span style="font-size:1.2rem;">🤖</span>
                <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;">AI Coach Tip</h2>
            </div>
            <p style="font-size:.85rem;color:rgba(255,255,255,.7);line-height:1.6;flex:1;overflow-y:auto;padding-bottom:1rem;">
                {{ $aiTip ?? "Stay hydrated and make sure to stretch after your workouts to improve recovery." }}
            </p>
        </div>

        {{-- Workout Music --}}
        <div class="fade-in-up delay-4" style="background:linear-gradient(135deg,rgba(236,72,153,.1),rgba(236,72,153,.02));border:1px solid rgba(236,72,153,.3);border-radius:20px;padding:1.6rem;position:relative;min-height:140px;display:flex;flex-direction:column;justify-content:center;text-align:center;">
            <div style="font-size:2.5rem;margin-bottom:.8rem;filter:drop-shadow(0 0 10px rgba(236,72,153,.4));">🎵</div>
            <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:.4rem;">Workout Music</h2>
            <p style="font-size:.78rem;color:rgba(255,255,255,.5);margin-bottom:1.2rem;">Pump up your energy with curated tracks.</p>
            <a href="{{ route('music.index') }}" style="background:rgba(236,72,153,.2);color:#f9a8d4;border:1px solid rgba(236,72,153,.4);padding:8px 16px;border-radius:10px;font-size:.8rem;font-weight:700;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='rgba(236,72,153,.3)';this.style.transform='scale(1.05)'" onmouseout="this.style.background='rgba(236,72,153,.2)';this.style.transform='scale(1)'">
                Open Player →
            </a>
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
                        <div style="border-bottom:1px solid rgba(139,92,246,.1);padding-bottom:.8rem;display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <p style="font-size:.88rem;font-weight:600;color:#e2d9f3;margin-bottom:2px;">{{ $workout->title }}</p>
                                <p style="font-size:.75rem;color:rgba(255,255,255,.3);">{{ $workout->type }} • {{ $workout->difficulty }}</p>
                            </div>
                            <div style="text-align:right;">
                                <p style="font-size:.8rem;color:#c4b5fd;font-weight:600;">{{ $workout->duration_minutes ?? 45 }} min</p>
                                <p style="font-size:.7rem;color:rgba(255,255,255,.4);">{{ $workout->calories_burned ?? random_int(200, 500) }} kcal</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:2rem 1rem;">
                    <div style="font-size:2rem;margin-bottom:.5rem;opacity:.4;">🏋️</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.85rem;margin-bottom:1rem;">No workouts yet</p>
                    <a href="{{ route('workouts.index') }}" style="display:inline-block;background:rgba(139,92,246,.2);color:#c4b5fd;padding:8px 16px;border-radius:8px;font-size:.8rem;font-weight:600;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='rgba(139,92,246,.3)'" onmouseout="this.style.background='rgba(139,92,246,.2)'">Log Your First Workout</a>
                </div>
            @endif
        </div>

        {{-- Upcoming Sessions --}}
        <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;margin-bottom:1.2rem;">📅 Upcoming Sessions</h2>
            @if(isset($upcomingSessions) && $upcomingSessions->count() > 0)
                <div style="display:flex;flex-direction:column;gap:.8rem;">
                    @foreach($upcomingSessions as $session)
                        <div style="border-bottom:1px solid rgba(139,92,246,.1);padding-bottom:.8rem;position:relative;">
                            <p style="font-size:.88rem;font-weight:600;color:#e2d9f3;margin-bottom:2px;">{{ $session->trainer->specialization ?? 'Personal Training' }} with {{ $session->trainer->name ?? 'Trainer' }}</p>
                            <p style="font-size:.75rem;color:rgba(255,255,255,.3);">{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y h:i A') }}</p>
                            <span style="font-size:.7rem;background:rgba(139,92,246,.15);color:#a78bfa;border:1px solid rgba(139,92,246,.25);padding:2px 8px;border-radius:50px;font-weight:600;display:inline-block;margin-top:4px;">{{ ucfirst($session->status) }}</span>
                            @php
                                $d = \Carbon\Carbon::parse($session->session_date);
                                $now = now();
                                if ($d->isFuture() && $d->diffInHours($now) < 24) {
                                    $diff = $now->diff($d);
                                    $countdown = $diff->h . 'h ' . $diff->i . 'm';
                                    echo '<span style="font-size:.7rem;color:#fca5a5;font-weight:600;position:absolute;bottom:0.8rem;right:0;">Starts in '.$countdown.'</span>';
                                } elseif ($d->isFuture()) {
                                    echo '<span style="font-size:.7rem;color:#fca5a5;font-weight:600;position:absolute;bottom:0.8rem;right:0;">In '.$d->diffInDays($now).' days</span>';
                                }
                            @endphp
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

    {{-- Weekly Calorie Burn Chart --}}
    <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.6rem;margin-bottom:2rem;">
        <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;margin-bottom:1.2rem;">🔥 Weekly Calorie Burn</h2>
        <div style="display:flex;align-items:flex-end;gap:8px;height:120px;padding-top:10px;">
            @php
                $maxCal = max($weeklyCalories ?? [1]);
            @endphp
            @foreach($weeklyCalories as $idx => $cal)
                @php
                    $height = ($cal / $maxCal) * 100;
                    $isCurrent = $idx === count($weeklyCalories) - 1;
                @endphp
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;">
                    <div style="flex:1;width:100%;display:flex;align-items:flex-end;justify-content:center;">
                        <div style="width:70%;height:{{ $height }}%;background:{{ $isCurrent ? 'linear-gradient(to top, #c4b5fd, #8b5cf6)' : 'rgba(139,92,246,.4)' }};border-radius:4px 4px 0 0;position:relative;transition:height .3s;">
                            <span style="position:absolute;top:-20px;left:50%;transform:translateX(-50%);font-size:.65rem;color:{{ $isCurrent ? '#c4b5fd' : 'rgba(255,255,255,.5)' }};font-weight:{{ $isCurrent ? '700' : 'normal' }};">{{ $cal }}</span>
                        </div>
                    </div>
                    <span style="font-size:.65rem;color:rgba(255,255,255,.4);">W{{ $idx + 1 }}</span>
                </div>
            @endforeach
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
                    <div style="background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.18);border-radius:16px;padding:1.2rem;text-align:center;transition:all .3s;position:relative;" onmouseover="this.style.borderColor='rgba(139,92,246,.4)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(139,92,246,.18)';this.style.transform=''">
                        @php $isAvailable = crc32($trainer->id ?? '1') % 2 == 0; @endphp
                        <div style="position:absolute;top:10px;right:10px;width:10px;height:10px;border-radius:50%;background:{{ $isAvailable ? '#10b981' : '#f59e0b' }};box-shadow:0 0 8px {{ $isAvailable ? '#10b981' : '#f59e0b' }};" title="{{ $isAvailable ? 'Available Today' : 'Busy' }}"></div>
                        <div style="font-size:2.2rem;margin-bottom:.5rem;">🏋️</div>
                        <h3 style="font-size:.9rem;font-weight:700;color:#e2d9f3;margin-bottom:2px;">{{ $trainer->name }}</h3>
                        <div style="margin-bottom:6px;display:flex;justify-content:center;gap:4px;flex-wrap:wrap;">
                            @php
                                $specs = explode(',', $trainer->specialization ?? 'Strength');
                            @endphp
                            @foreach($specs as $spec)
                                <span style="font-size:.65rem;background:rgba(139,92,246,.15);color:#c4b5fd;padding:2px 6px;border-radius:4px;border:1px solid rgba(139,92,246,.2);">{{ trim($spec) }}</span>
                            @endforeach
                        </div>
                        <p style="font-size:.82rem;font-weight:700;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:2px;">₹{{ $trainer->hourly_rate ?? 500 }}/hr</p>
                        @if(($trainer->total_clients ?? 0) > 0)
                            <p style="font-size:.72rem;color:rgba(255,255,255,.25);margin-bottom:.9rem;">⭐ {{ $trainer->rating ?? '4.8' }} ({{ $trainer->total_clients }} clients)</p>
                        @else
                            <p style="font-size:.72rem;color:rgba(255,255,255,.25);margin-bottom:.9rem;">⭐ {{ $trainer->rating ?? '4.8' }}</p>
                        @endif
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
