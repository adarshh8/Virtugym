@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<style>
    .tab-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: .85rem;
        font-weight: 600;
        color: var(--vg-text-muted);
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .tab-btn.active {
        background: var(--vg-accent-soft);
        color: var(--vg-text-strong);
    }
    .tab-btn:hover:not(.active) {
        color: var(--vg-text-strong);
    }
    .booking-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 24px;
        padding: 1.8rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s;
        position: relative;
    }
    .booking-card:hover {
        border-color: rgba(139, 92, 246, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .past-session-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.2rem;
        background: rgba(255, 255, 255, 0.01);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 16px;
        margin-bottom: .8rem;
        transition: all 0.2s;
    }
    .past-session-item:hover {
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.03);
    }
    .stat-badge {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        padding: 1.5rem;
        text-align: center;
    }
    .timeline-item {
        position: relative;
        padding-left: 24px;
        border-left: 2px solid rgba(139, 92, 246, 0.2);
        padding-bottom: 1.5rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--vg-accent);
        box-shadow: 0 0 8px var(--vg-accent-glow);
    }
    .countdown-badge {
        background: linear-gradient(135deg, rgba(139,92,246,0.1) 0%, rgba(249,168,212,0.05) 100%);
        border: 1px solid rgba(139, 92, 246, 0.2);
        color: #c4b5fd;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .action-btn {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        color: #fff;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: .8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .action-btn:hover {
        background: rgba(255,255,255,0.1);
        border-color: rgba(255,255,255,0.2);
    }
    .action-btn.primary {
        background: var(--vg-gradient);
        border: none;
        box-shadow: 0 4px 15px var(--vg-accent-glow);
    }
    .action-btn.primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px var(--vg-accent-glow);
    }
    .action-btn.danger {
        background: rgba(244,63,94,0.1);
        border-color: rgba(244,63,94,0.2);
        color: #f43f5e;
    }
    .action-btn.danger:hover {
        background: rgba(244,63,94,0.2);
    }
    @media(max-width: 992px) {
        .layout-container { flex-direction: column; }
        .summary-panel { width: 100% !important; margin-top: 2rem; }
    }
</style>

{{-- Top Row: Stats (Trainer Only) --}}
@if($isTrainer && isset($trainerStats))
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:1.5rem;margin-bottom:2.5rem;" class="fade-in-up">
        <div class="stat-badge">
            <p style="font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Today's Sessions</p>
            <p style="font-size:2rem;font-weight:900;color:#fff;margin-bottom:0;">{{ $trainerStats['todays_sessions'] }}</p>
            @if($trainerStats['todays_sessions'] == 0 && isset($upcomingBookings) && $upcomingBookings->count() > 0)
                @php
                    $nextSession = \Carbon\Carbon::parse($upcomingBookings->first()->session_date);
                    $daysToNext = now()->diffInDays($nextSession, false);
                    $daysToNext = $daysToNext < 0 ? 0 : floor($daysToNext);
                    $nextText = $daysToNext == 0 ? 'later today' : ($daysToNext == 1 ? 'tomorrow' : "in $daysToNext days");
                @endphp
                <p style="font-size:.7rem;color:var(--vg-accent);margin-top:4px;font-weight:600;">next session {{ $nextText }}</p>
            @endif
        </div>
        <div class="stat-badge">
            <p style="font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">This Week's Earnings</p>
            <p style="font-size:2rem;font-weight:900;color:#10b981;margin-bottom:0;">₹{{ number_format($trainerStats['weekly_earnings']) }}</p>
        </div>
        <div class="stat-badge">
            <p style="font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Total Upcoming</p>
            <p style="font-size:2rem;font-weight:900;color:var(--vg-accent);margin-bottom:0;">{{ $trainerStats['total_upcoming'] }}</p>
        </div>
    </div>
@endif

<div class="layout-container" style="max-width:1400px;margin:0 auto;display:flex;gap:2rem;align-items:flex-start;">
    
    {{-- Main Content Area --}}
    <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1.5rem;">
            <div>
                <h1 style="font-size:1.8rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.3rem;">My Bookings 📅</h1>
                <p style="color:var(--vg-text-muted);font-size:.85rem;">Manage your training sessions</p>
            </div>
            
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                {{-- Trainee Filter (Trainer Only) --}}
                @if($isTrainer && isset($uniqueTrainees) && $uniqueTrainees->count() > 0)
                    <form method="GET" action="{{ route('bookings.index') }}" id="filterForm">
                        <select name="trainee_id" onchange="document.getElementById('filterForm').submit()" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:8px 16px;color:#fff;font-size:.85rem;outline:none;cursor:pointer;">
                            <option value="">All Trainees</option>
                            @foreach($uniqueTrainees as $trainee)
                                <option value="{{ $trainee->id }}" {{ request('trainee_id') == $trainee->id ? 'selected' : '' }}>
                                    {{ $trainee->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                {{-- Filter Tabs --}}
                <div style="display:flex;gap:4px;background:var(--vg-sidebar);padding:6px;border-radius:12px;border:1px solid var(--vg-border);">
                    <button onclick="switchTab('upcoming')" id="tab-upcoming" class="tab-btn active">Upcoming</button>
                    <button onclick="switchTab('past')" id="tab-past" class="tab-btn">Past</button>
                    <button onclick="switchTab('cancelled')" id="tab-cancelled" class="tab-btn">Cancelled</button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div style="background:rgba(16,185,129,.1);border-left:4px solid #10b981;color:#10b981;padding:1rem;border-radius:8px;margin-bottom:1.5rem;font-size:.85rem;font-weight:600;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:rgba(244,63,94,.1);border-left:4px solid #f43f5e;color:#f43f5e;padding:1rem;border-radius:8px;margin-bottom:1.5rem;font-size:.85rem;font-weight:600;">
                {{ session('error') }}
            </div>
        @endif

        {{-- UPCOMING SESSIONS TAB --}}
        <div id="content-upcoming" style="display:block;" class="fade-in-up">
            @if(isset($upcomingBookings) && $upcomingBookings->count() > 0)
                @if($isTrainer && $upcomingBookings->count() >= 3)
                    {{-- Bulk Actions Bar --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.04);padding:1rem 1.5rem;border-radius:16px;margin-bottom:1.5rem;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <input type="checkbox" id="selectAllBookings" style="width:18px;height:18px;cursor:pointer;accent-color:var(--vg-accent);">
                            <span style="font-size:.85rem;color:rgba(255,255,255,0.6);font-weight:600;">Select All</span>
                        </div>
                        <form method="POST" action="{{ route('bookings.bulk-complete') }}" id="bulkCompleteForm">
                            @csrf
                            <input type="hidden" name="booking_ids[]" id="bulkBookingIds">
                            <button type="submit" onclick="return prepareBulkSubmit()" class="action-btn primary" style="font-size:.75rem;padding:6px 14px;">
                                <i data-lucide="check-square" style="width:14px;height:14px;"></i> Mark Completed
                            </button>
                        </form>
                    </div>
                @endif

                @foreach($upcomingBookings as $booking)
                    @php
                        $sessionTime = strtotime($booking->session_date);
                        $now = time();
                        $canJoin = ($now >= $sessionTime - 900); // 15 minutes before
                        
                        $partner = $isTrainer ? ($booking->trainee ?? null) : ($booking->trainer ?? null);
                        $partnerName = $partner ? $partner->name : ($isTrainer ? 'Trainee' : 'Trainer');
                        $initial = substr($partnerName, 0, 1);
                        $avatarColor = (crc32($booking->id ?? '1') % 2 == 0) ? '#8b5cf6' : '#10b981';
                    @endphp
                    <div class="booking-card" id="booking-{{ $booking->id }}">
                        <div style="display:flex;gap:1.5rem;align-items:flex-start;">
                            
                            @if($isTrainer)
                                <div style="padding-top:14px;">
                                    <input type="checkbox" class="booking-selector" value="{{ $booking->id }}" style="width:18px;height:18px;cursor:pointer;accent-color:var(--vg-accent);">
                                </div>
                            @endif

                            <div style="flex:1;display:flex;flex-direction:column;gap:1.5rem;">
                                
                                {{-- Top Section: Trainee Details & Badges --}}
                                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;border-bottom:1px solid rgba(255,255,255,0.03);padding-bottom:1rem;">
                                    <div style="display:flex;gap:1rem;align-items:center;">
                                        <div style="width:48px;height:48px;border-radius:50%;background:{{ $avatarColor }};display:flex;align-items:center;justify-content:center;font-size:1.25rem;font-weight:700;color:#fff;flex-shrink:0;">
                                            {{ $initial }}
                                        </div>
                                        <div>
                                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                                <h3 style="font-size:1.15rem;font-weight:700;color:var(--vg-text-strong);">{{ $partnerName }}</h3>
                                                
                                                {{-- Confirmed Badge --}}
                                                <span style="font-size:.65rem;background:rgba(16,185,129,.15);color:#10b981;border:1px solid rgba(16,185,129,.3);padding:2px 10px;border-radius:50px;font-weight:700;text-transform:uppercase;letter-spacing:.02em;">
                                                    <i data-lucide="check-circle" style="width:10px;height:10px;display:inline-block;margin-bottom:-1px;"></i> Confirmed
                                                </span>
                                                
                                                {{-- Session Type Tag --}}
                                                <span style="font-size:.65rem;background:rgba(139,92,246,.15);color:#c4b5fd;border:1px solid rgba(139,92,246,.3);padding:2px 10px;border-radius:50px;font-weight:700;text-transform:uppercase;letter-spacing:.02em;">
                                                    {{ $booking->session_type ?? 'Strength' }}
                                                </span>
                                            </div>
                                            
                                            <div style="display:flex;gap:12px;margin-top:4px;align-items:center;">
                                                <button onclick="viewTraineeProfile('{{ $partnerName }}', '{{ $partner->email ?? '' }}', '{{ $booking->session_type ?? 'Strength' }}')" style="background:none;border:none;color:var(--vg-accent);font-size:.75rem;font-weight:600;padding:0;cursor:pointer;text-decoration:underline;">View Profile</button>
                                                <span style="color:rgba(255,255,255,0.15);">|</span>
                                                <a href="{{ route('chat.index', $partner->id ?? '') }}" style="color:rgba(255,255,255,0.4);font-size:.75rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                                                    <i data-lucide="message-square" style="width:12px;height:12px;"></i> Chat Shortcut
                                                </a>
                                                
                                                @if($isTrainer && isset($pastBookings))
                                                    @php
                                                        $lastSession = $pastBookings->where('trainee_id', $booking->trainee_id)->where('status', 'completed')->first();
                                                    @endphp
                                                    @if($lastSession)
                                                        <span style="color:rgba(255,255,255,0.15);">|</span>
                                                        <span style="font-size:.7rem;color:rgba(255,255,255,0.5);font-style:italic;">
                                                            Last session: {{ \Carbon\Carbon::parse($lastSession->session_date)->format('M d') }}
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Countdown Badge --}}
                                    @if(!$canJoin)
                                        @php
                                            $d = \Carbon\Carbon::parse($booking->session_date);
                                            $nowDate = now();
                                            $totalMinutes = $nowDate->diffInMinutes($d);
                                            $days = intdiv($totalMinutes, 1440); // 1440 minutes in a day
                                            $remainingMinutes = $totalMinutes % 1440;
                                            $hours = intdiv($remainingMinutes, 60);
                                            $minutes = $remainingMinutes % 60;
                                            
                                            $countdown = 'Starts in ';
                                            if ($days > 0) {
                                                $countdown .= $days . 'd';
                                                if ($hours > 0) {
                                                    $countdown .= ' ' . $hours . 'h';
                                                }
                                            } else {
                                                $countdown .= $hours . 'h ' . $minutes . 'm';
                                            }
                                        @endphp
                                        <div class="countdown-badge">
                                            <i data-lucide="bell" style="width:12px;height:12px;"></i> {{ $countdown }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Middle Section: Booking Details --}}
                                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:1.5rem;">
                                    <div>
                                        <p style="font-size:.7rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Date & Time</p>
                                        <p style="font-size:.9rem;color:var(--vg-text-strong);font-weight:700;">{{ \Carbon\Carbon::parse($booking->session_date)->format('M d, Y • h:i A') }}</p>
                                    </div>
                                    <div>
                                        <p style="font-size:.7rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Duration</p>
                                        <p style="font-size:.9rem;color:var(--vg-text-strong);font-weight:700;">{{ $booking->duration_minutes }} min</p>
                                    </div>
                                    <div>
                                        <p style="font-size:.7rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Session Cost</p>
                                        <p style="font-size:.9rem;color:#10b981;font-weight:700;">₹{{ number_format($booking->amount) }}</p>
                                    </div>
                                </div>

                                {{-- Notes Area (Trainer Only) --}}
                                @if($isTrainer)
                                    <div style="background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.04);border-radius:16px;padding:1.2rem;">
                                        <form method="POST" action="{{ route('bookings.save-notes', $booking->id) }}">
                                            @csrf
                                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                                                <p style="font-size:.75rem;color:rgba(255,255,255,0.4);font-weight:700;text-transform:uppercase;display:flex;align-items:center;gap:6px;margin:0;">
                                                    <i data-lucide="clipboard-list" style="width:14px;height:14px;color:var(--vg-accent);"></i> Trainer's Private Notes
                                                </p>
                                                <select name="session_type" style="background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);border-radius:6px;padding:4px 8px;color:#fff;font-size:.75rem;outline:none;cursor:pointer;">
                                                    <option value="Strength" {{ ($booking->session_type ?? '') == 'Strength' ? 'selected' : '' }}>Strength & Conditioning</option>
                                                    <option value="Cardio" {{ ($booking->session_type ?? '') == 'Cardio' ? 'selected' : '' }}>Cardio / HIIT</option>
                                                    <option value="Flexibility" {{ ($booking->session_type ?? '') == 'Flexibility' ? 'selected' : '' }}>Yoga / Flexibility</option>
                                                    <option value="General" {{ ($booking->session_type ?? '') == 'General' ? 'selected' : '' }}>General Fitness</option>
                                                </select>
                                            </div>
                                            <textarea name="notes" placeholder="Write session focus, client progress, injuries or goals here..." rows="2" style="width:100%;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:8px 12px;color:#fff;font-family:inherit;font-size:.8rem;outline:none;resize:vertical;margin-bottom:8px;">{{ $booking->trainer_notes }}</textarea>
                                            <div style="display:flex;justify-content:flex-end;">
                                                <button type="submit" class="action-btn" style="font-size:.7rem;padding:4px 10px;">Save Session Details</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                {{-- Action Section --}}
                                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-top:0.5rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.03);">
                                    <div style="display:flex;gap:8px;">
                                        @if($canJoin)
                                            <a href="{{ route('video-call.join', $booking->id) }}" class="action-btn primary">
                                                🎥 Join Video Session
                                            </a>
                                        @else
                                            <button disabled class="action-btn" style="opacity:0.6;cursor:not-allowed;background:rgba(255,255,255,0.02);">
                                                🎥 Video Call (opens 15 min before)
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <div style="display:flex;gap:8px;align-items:center;">
                                        @if($isTrainer)
                                            <form method="POST" action="{{ route('bookings.update', $booking->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="action-btn" style="border-color:#10b981;color:#10b981;">Mark Complete</button>
                                            </form>
                                        @endif

                                        @if($isTrainer)
                                            <button onclick="openRescheduleModal('{{ $booking->id }}')" class="action-btn">Reschedule</button>
                                            <form method="POST" action="{{ route('bookings.update', $booking->id) }}" onsubmit="return confirmTrainerCancellation(this)">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="cancelled">
                                                <input type="hidden" name="cancellation_reason" value="">
                                                <button type="submit" class="action-btn danger">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state-container">
                    <div style="font-size:2.5rem;margin-bottom:.8rem;opacity:.5;">📅</div>
                    <h3 style="color:var(--vg-text-strong);font-size:1.1rem;font-weight:700;margin-bottom:.4rem;">No upcoming sessions</h3>
                    <p style="color:var(--vg-text-muted);font-size:.85rem;margin-bottom:1.2rem;">
                        {{ $isTrainer ? 'You have no confirmed bookings right now.' : 'Ready to train? Book a session to get started.' }}
                    </p>
                    @if(!$isTrainer)
                        <a href="{{ route('trainee.trainers') }}" style="display:inline-block;background:var(--vg-accent-soft);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:8px 20px;border-radius:10px;font-size:.8rem;font-weight:600;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--vg-accent-glow)'" onmouseout="this.style.background='var(--vg-accent-soft)'">Browse Trainers</a>
                    @endif
                </div>
            @endif
        </div>

        {{-- PAST SESSIONS TAB --}}
        <div id="content-past" style="display:none;" class="fade-in-up">
            @if(isset($pastBookings) && $pastBookings->count() > 0)
                <div style="display:flex;flex-direction:column;">
                    @foreach($pastBookings as $booking)
                        @php
                            $partner = $isTrainer ? ($booking->trainee ?? null) : ($booking->trainer ?? null);
                            $partnerName = $partner ? $partner->name : ($isTrainer ? 'Trainee' : 'Trainer');
                        @endphp
                        <div class="past-session-item">
                            <div>
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;">
                                    <p style="font-size:.95rem;font-weight:700;color:var(--vg-text-strong);">{{ $partnerName }}</p>
                                    <span style="font-size:.6rem;background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.6);padding:2px 8px;border-radius:50px;font-weight:600;text-transform:uppercase;">
                                        {{ $booking->session_type ?? 'Strength' }}
                                    </span>
                                </div>
                                <p style="font-size:.75rem;color:var(--vg-text-muted);">{{ \Carbon\Carbon::parse($booking->session_date)->format('M d, Y') }} • {{ $booking->duration_minutes }} min</p>
                            </div>
                            
                            <div style="display:flex;gap:1.5rem;align-items:center;">
                                <div style="text-align:right;">
                                    <p style="font-size:.85rem;color:var(--vg-text-strong);font-weight:600;">₹{{ number_format($booking->amount) }}</p>
                                    <p style="font-size:.7rem;color:#10b981;">{{ ucfirst($booking->status) }}</p>
                                </div>
                                @if(!$isTrainer && $partner)
                                    <a href="{{ route('book.trainer.create', $partner->id) }}" class="past-action-btn" style="background:var(--vg-sidebar);border:1px solid var(--vg-border-strong);color:var(--vg-text-strong);padding:6px 12px;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--vg-panel-strong)'" onmouseout="this.style.background='var(--vg-sidebar)'">Book Again</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:3rem 1rem;background:var(--vg-panel);border:1px dashed var(--vg-border-strong);border-radius:16px;">
                    <div style="font-size:2rem;margin-bottom:.5rem;opacity:.4;">⏳</div>
                    <p style="color:var(--vg-text-muted);font-size:.85rem;">No past sessions found.</p>
                </div>
            @endif
        </div>

        {{-- CANCELLED SESSIONS TAB --}}
        <div id="content-cancelled" style="display:none;" class="fade-in-up">
            @if(isset($cancelledBookings) && $cancelledBookings->count() > 0)
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;">
                    @foreach($cancelledBookings as $booking)
                        @php
                            $partner = $isTrainer ? ($booking->trainee ?? null) : ($booking->trainer ?? null);
                            $partnerName = $partner ? $partner->name : ($isTrainer ? 'Trainee' : 'Trainer');
                        @endphp
                        <div style="background:var(--vg-panel);border:1px solid rgba(244,63,94,.2);border-radius:16px;padding:1.2rem;position:relative;">
                            <span style="position:absolute;top:1rem;right:1rem;font-size:.7rem;color:#f43f5e;background:rgba(244,63,94,.1);padding:2px 8px;border-radius:4px;font-weight:700;">Cancelled</span>
                            <h3 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:4px;">{{ $partnerName }}</h3>
                            <p style="font-size:.8rem;color:var(--vg-text-muted);margin-bottom:12px;">{{ \Carbon\Carbon::parse($booking->session_date)->format('M d, Y • h:i A') }}</p>
                            @if($booking->cancellation_reason)
                                <div style="background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:.8rem;margin-bottom:12px;">
                                    <p style="font-size:.68rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;font-weight:800;margin-bottom:4px;">Cancellation Reason</p>
                                    <p style="font-size:.82rem;color:var(--vg-text-strong);line-height:1.45;">{{ $booking->cancellation_reason }}</p>
                                </div>
                            @endif
                            @if(!$isTrainer && ($booking->refund_status ?? null))
                                <p style="font-size:.75rem;color:var(--vg-text-muted);margin-bottom:12px;">
                                    Refund status:
                                    <span style="color:{{ ($booking->refund_status ?? '') === 'processed' ? '#10b981' : '#fbbf24' }};font-weight:700;">
                                        {{ str_replace('_', ' ', ucfirst($booking->refund_status)) }}
                                    </span>
                                </p>
                            @endif
                            @if(!$isTrainer && $partner)
                                <a href="{{ route('book.trainer.create', $partner->id) }}" style="display:inline-block;font-size:.75rem;color:var(--vg-accent);font-weight:600;text-decoration:none;">Rebook Session →</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:3rem 1rem;background:var(--vg-panel);border:1px dashed var(--vg-border-strong);border-radius:16px;">
                    <div style="font-size:2rem;margin-bottom:.5rem;opacity:.4;">✅</div>
                    <p style="color:var(--vg-text-muted);font-size:.85rem;">You have no cancelled sessions.</p>
                </div>
            @endif
        </div>

    </div>

    {{-- Right Sidebar --}}
    <div class="summary-panel" style="width:340px;flex-shrink:0;">
        @if(!$isTrainer)
            <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.6rem;position:sticky;top:2rem;">
                <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.5rem;">📊 Monthly Summary</h2>
                
                <div style="margin-bottom:1.5rem;">
                    <p style="font-size:.75rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Sessions Completed</p>
                    <p style="font-size:2rem;font-weight:900;color:var(--vg-text-strong);line-height:1;">{{ $totalSessionsCompleted > 0 ? $totalSessionsCompleted : '—' }}</p>
                </div>

                @php
                    $lifetimeSessions = \App\Models\Booking::where('trainee_id', Auth::id())->where('status', 'completed')->count();
                @endphp
                <div style="margin-bottom:1.5rem;">
                    <p style="font-size:.75rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Total Sessions (Lifetime)</p>
                    <p style="font-size:2rem;font-weight:900;color:var(--vg-accent);line-height:1;">{{ $lifetimeSessions > 0 ? $lifetimeSessions : '—' }}</p>
                </div>
                
                <div>
                    <p style="font-size:.75rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Total Spent</p>
                    <p style="font-size:2rem;font-weight:900;color:#10b981;line-height:1;">₹{{ number_format($totalSpentThisMonth) }}</p>
                    <p style="font-size:.7rem;color:var(--vg-text-faint);margin-top:4px;">Current month ({{ now()->format('F') }})</p>
                </div>
            </div>
        @else
            {{-- Trainer Right Sidebar (Today's Timeline & Earnings Summary) --}}
            <div style="background:rgba(255, 255, 255, 0.02);border:1px solid rgba(255, 255, 255, 0.06);border-radius:24px;padding:1.8rem;position:sticky;top:2rem;display:flex;flex-direction:column;gap:2rem;">
                
                {{-- Today's Schedule Timeline --}}
                <div>
                    <h2 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:8px;">
                        <i data-lucide="activity" style="color:var(--vg-accent);"></i> Today's Schedule
                    </h2>
                    
                    @if(isset($todaysSchedule) && $todaysSchedule->count() > 0)
                        <div style="display:flex;flex-direction:column;">
                            @foreach($todaysSchedule as $session)
                                <div class="timeline-item">
                                    <p style="font-size:.85rem;font-weight:700;color:#fff;margin-bottom:2px;">{{ $session->trainee->name ?? 'Trainee' }}</p>
                                    <p style="font-size:.75rem;color:rgba(255,255,255,0.4);font-weight:600;margin-bottom:4px;">
                                        {{ \Carbon\Carbon::parse($session->session_date)->format('h:i A') }} • {{ $session->duration_minutes }}m
                                    </p>
                                    <span style="font-size:.6rem;background:rgba(139,92,246,0.1);color:#c4b5fd;padding:2px 8px;border-radius:4px;font-weight:700;text-transform:uppercase;">
                                        {{ $session->session_type ?? 'Strength' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="font-size:.8rem;color:rgba(255,255,255,0.25);font-style:italic;">No training sessions scheduled for today.</p>
                    @endif
                </div>

                {{-- Quick Earnings Card --}}
                <div style="border-top:1px solid rgba(255,255,255,0.04);padding-top:1.5rem;">
                    <h2 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:1.2rem;display:flex;align-items:center;gap:8px;">
                        <i data-lucide="wallet" style="color:#10b981;"></i> Weekly Earnings
                    </h2>
                    <div style="background:rgba(0,0,0,0.2);padding:1.2rem;border-radius:16px;border:1px solid rgba(255,255,255,0.04);text-align:center;">
                        <p style="font-size:.65rem;color:rgba(255,255,255,0.4);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Weekly Revenue Goal (₹20,000)</p>
                        <p style="font-size:1.8rem;font-weight:900;color:#10b981;margin-bottom:10px;">
                            ₹{{ number_format($trainerStats['weekly_earnings'] ?? 0) }}
                        </p>
                        
                        @php
                            $target = 20000;
                            $pct = min(100, round((($trainerStats['weekly_earnings'] ?? 0) / $target) * 100));
                        @endphp
                        <div style="width:100%;height:10px;background:rgba(255,255,255,0.05);border-radius:5px;overflow:hidden;margin-bottom:6px;">
                            <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg, #10b981, var(--vg-accent));border-radius:5px;"></div>
                        </div>
                        <span style="font-size:.7rem;color:rgba(255,255,255,0.3);font-weight:600;">{{ $pct }}% of weekly goal achieved</span>
                    </div>
                </div>

            </div>
        @endif
    </div>
</div>

{{-- Trainee Profile Modal --}}
<div id="profileModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);backdrop-filter:blur(10px);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:rgba(20,20,20,.95);border:1px solid rgba(255,255,255,.1);border-radius:28px;padding:2.5rem;width:100%;max-width:440px;position:relative;text-align:center;">
        <button onclick="closeProfileModal()" style="position:absolute;top:1.5rem;right:1.5rem;background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;">
            <i data-lucide="x"></i>
        </button>
        <div id="modalAvatar" style="width:70px;height:70px;border-radius:50%;background:var(--vg-accent);margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;color:#fff;"></div>
        <h2 id="modalName" style="font-size:1.4rem;font-weight:900;color:#fff;margin-bottom:4px;">Trainee Name</h2>
        <p id="modalEmail" style="font-size:.85rem;color:rgba(255,255,255,0.4);margin-bottom:1.5rem;">email@example.com</p>
        
        <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);border-radius:16px;padding:1rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;text-align:left;margin-bottom:1.5rem;">
            <div>
                <p style="font-size:.65rem;color:rgba(255,255,255,0.4);font-weight:700;text-transform:uppercase;">Primary Focus</p>
                <p id="modalFocus" style="font-size:.85rem;color:#fff;font-weight:600;">Cardio</p>
            </div>
            <div>
                <p style="font-size:.65rem;color:rgba(255,255,255,0.4);font-weight:700;text-transform:uppercase;">Goal Metrics</p>
                <p style="font-size:.85rem;color:#10b981;font-weight:600;">Active Tracker</p>
            </div>
        </div>
        
        <button onclick="closeProfileModal()" class="action-btn primary" style="width:100%;justify-content:center;">Close Profile</button>
    </div>
</div>

{{-- Reschedule Modal --}}
<div id="rescheduleModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);backdrop-filter:blur(10px);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:rgba(20,20,20,.95);border:1px solid rgba(255,255,255,.1);border-radius:28px;padding:2.5rem;width:100%;max-width:440px;position:relative;">
        <button onclick="closeRescheduleModal()" style="position:absolute;top:1.5rem;right:1.5rem;background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;">
            <i data-lucide="x"></i>
        </button>
        <h2 style="font-size:1.4rem;font-weight:900;color:#fff;margin-bottom:8px;">Reschedule Session</h2>
        <p style="font-size:.85rem;color:rgba(255,255,255,0.4);margin-bottom:1.5rem;">Please contact your trainee via chat to agree on a new time, or select a slot below to propose a change.</p>
        
        <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);border-radius:16px;padding:1.5rem;text-align:center;margin-bottom:1.5rem;">
            <i data-lucide="calendar-clock" style="width:40px;height:40px;color:var(--vg-accent);margin-bottom:10px;"></i>
            <p style="font-size:.9rem;color:#fff;font-weight:600;margin-bottom:6px;">Automated Rescheduling</p>
            <p style="font-size:.75rem;color:rgba(255,255,255,0.5);">This feature is currently in beta. Please use direct messaging to coordinate timing changes for now.</p>
        </div>
        
        <div style="display:flex;gap:12px;">
            <button onclick="closeRescheduleModal()" class="action-btn" style="flex:1;justify-content:center;">Cancel</button>
            <a href="{{ route('chat.index') }}" class="action-btn primary" style="flex:1;justify-content:center;text-decoration:none;">Open Chat</a>
        </div>
    </div>
</div>

<script>
    // Tab switching
    function switchTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById('tab-' + tabName).classList.add('active');
        
        document.getElementById('content-upcoming').style.display = 'none';
        document.getElementById('content-past').style.display = 'none';
        document.getElementById('content-cancelled').style.display = 'none';
        
        document.getElementById('content-' + tabName).style.display = 'block';
    }

    // View Trainee Profile Modal
    function viewTraineeProfile(name, email, focus) {
        document.getElementById('modalAvatar').innerText = name.charAt(0);
        document.getElementById('modalName').innerText = name;
        document.getElementById('modalEmail').innerText = email || 'No email provided';
        document.getElementById('modalFocus').innerText = focus || 'General Fitness';
        document.getElementById('profileModal').style.display = 'flex';
    }

    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }

    // Reschedule Modal
    function openRescheduleModal(bookingId) {
        document.getElementById('rescheduleModal').style.display = 'flex';
    }

    function closeRescheduleModal() {
        document.getElementById('rescheduleModal').style.display = 'none';
    }

    // Bulk action select all
    const selectAll = document.getElementById('selectAllBookings');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const selectors = document.querySelectorAll('.booking-selector');
            selectors.forEach(sel => sel.checked = selectAll.checked);
        });
    }

    function prepareBulkSubmit() {
        const checkedSelectors = document.querySelectorAll('.booking-selector:checked');
        if (checkedSelectors.length === 0) {
            alert('Please select at least one booking to complete.');
            return false;
        }

        const idsInput = document.getElementById('bulkBookingIds');
        // Clear previous entries
        const form = document.getElementById('bulkCompleteForm');
        // Remove existing dynamic inputs
        form.querySelectorAll('.dynamic-id-input').forEach(input => input.remove());

        // Append inputs dynamically
        checkedSelectors.forEach(selector => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'booking_ids[]';
            input.value = selector.value;
            input.className = 'dynamic-id-input';
            form.appendChild(input);
        });

        return confirm(`Are you sure you want to mark ${checkedSelectors.length} sessions as completed?`);
    }

    function confirmTrainerCancellation(form) {
        const reason = prompt('Please enter the cancellation reason. Admin will review the trainee refund request.');

        if (!reason || reason.trim().length < 5) {
            alert('Cancellation reason must be at least 5 characters.');
            return false;
        }

        form.querySelector('input[name="cancellation_reason"]').value = reason.trim();
        return confirm('Cancel this session and send a refund request to admin?');
    }

    // Close modal on click outside
    window.onclick = function(event) {
        const profileModal = document.getElementById('profileModal');
        const rescheduleModal = document.getElementById('rescheduleModal');
        if (event.target == profileModal) {
            closeProfileModal();
        }
        if (event.target == rescheduleModal) {
            closeRescheduleModal();
        }
    }
</script>
@endsection
