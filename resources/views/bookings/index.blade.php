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
        background: var(--vg-panel);
        border: 1px solid var(--vg-border);
        border-radius: 20px;
        padding: 1.6rem;
        margin-bottom: 1.2rem;
        transition: all 0.3s;
    }
    .booking-card:hover {
        border-color: var(--vg-border-strong);
        transform: translateY(-2px);
    }
    .past-session-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: var(--vg-panel);
        border: 1px solid var(--vg-border);
        border-radius: 16px;
        margin-bottom: .8rem;
    }
    @media(max-width: 768px) {
        .layout-container { flex-direction: column; }
        .summary-panel { width: 100% !important; order: -1; margin-bottom: 2rem; }
        .past-session-item { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .past-action-btn { width: 100%; text-align: center; }
    }
</style>

<div class="layout-container" style="max-width:1400px;margin:0 auto;display:flex;gap:2rem;align-items:flex-start;">
    
    {{-- Main Content Area --}}
    <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <h1 style="font-size:1.8rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.3rem;">My Bookings 📅</h1>
                <p style="color:var(--vg-text-muted);font-size:.85rem;">Manage your training sessions</p>
            </div>
            
            {{-- Filter Tabs --}}
            <div style="display:flex;gap:4px;background:var(--vg-sidebar);padding:6px;border-radius:12px;border:1px solid var(--vg-border);">
                <button onclick="switchTab('upcoming')" id="tab-upcoming" class="tab-btn active">Upcoming</button>
                <button onclick="switchTab('past')" id="tab-past" class="tab-btn">Past</button>
                <button onclick="switchTab('cancelled')" id="tab-cancelled" class="tab-btn">Cancelled</button>
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
                    <div class="booking-card">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1.5rem;">
                            
                            {{-- Info Section --}}
                            <div style="display:flex;gap:1rem;align-items:flex-start;">
                                <div style="width:50px;height:50px;border-radius:50%;background:{{ $avatarColor }};display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                                        <h3 style="font-size:1.1rem;font-weight:700;color:var(--vg-text-strong);">{{ $partnerName }}</h3>
                                        @if(!$isTrainer)
                                            <span style="font-size:.65rem;background:rgba(139,92,246,.15);color:#a78bfa;border:1px solid rgba(139,92,246,.3);padding:2px 8px;border-radius:50px;font-weight:600;">{{ $partner->specialization ?? 'Personal Training' }}</span>
                                            <span style="font-size:.65rem;background:rgba(245,158,11,.15);color:#fbbf24;border:1px solid rgba(245,158,11,.3);padding:2px 8px;border-radius:50px;font-weight:600;">★ {{ $partner->rating ?? '5.0' }}</span>
                                        @endif
                                    </div>
                                    
                                    <div style="display:flex;gap:1.5rem;margin-top:10px;flex-wrap:wrap;">
                                        <div>
                                            <p style="font-size:.7rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Date & Time</p>
                                            <p style="font-size:.85rem;color:var(--vg-text-strong);font-weight:600;">{{ \Carbon\Carbon::parse($booking->session_date)->format('M d, Y • h:i A') }}</p>
                                        </div>
                                        <div>
                                            <p style="font-size:.7rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Duration</p>
                                            <p style="font-size:.85rem;color:var(--vg-text-strong);font-weight:600;">{{ $booking->duration_minutes }} min</p>
                                        </div>
                                        <div>
                                            <p style="font-size:.7rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Cost</p>
                                            <p style="font-size:.85rem;color:var(--vg-text-strong);font-weight:600;">₹{{ number_format($booking->amount) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Action Section --}}
                            <div style="text-align:right;min-width:200px;">
                                <div style="margin-bottom:1rem;">
                                    <span style="display:inline-block;background:rgba(16,185,129,.15);color:#10b981;border:1px solid rgba(16,185,129,.3);padding:4px 12px;border-radius:50px;font-size:.75rem;font-weight:700;">✓ Confirmed</span>
                                </div>
                                
                                @if($canJoin)
                                    <a href="{{ route('video-call.join', $booking->id) }}" style="display:inline-block;width:100%;text-align:center;background:var(--vg-accent);color:var(--vg-text-strong);padding:8px 16px;border-radius:8px;font-size:.8rem;font-weight:700;text-decoration:none;margin-bottom:8px;transition:all .2s;" onmouseover="this.style.background='var(--vg-accent-glow)'" onmouseout="this.style.background='var(--vg-accent)'">
                                        🎥 Join Video Session
                                    </a>
                                @else
                                    @php
                                        $d = \Carbon\Carbon::parse($booking->session_date);
                                        $nowDate = now();
                                        $diff = $nowDate->diff($d);
                                        $countdown = '';
                                        if ($d->diffInDays($nowDate) > 0) {
                                            $countdown = 'Starts in ' . $d->diffInDays($nowDate) . ' days';
                                        } else {
                                            $countdown = 'Starts in ' . $diff->h . 'h ' . $diff->i . 'm';
                                        }
                                    @endphp
                                    <div style="display:inline-block;width:100%;text-align:center;background:var(--vg-sidebar);border:1px solid var(--vg-border-strong);color:var(--vg-text-muted);padding:8px 16px;border-radius:8px;font-size:.8rem;font-weight:600;margin-bottom:8px;">
                                        ⏳ {{ $countdown }}
                                    </div>
                                @endif
                                
                                <div style="display:flex;gap:8px;">
                                    <button style="flex:1;background:transparent;border:1px solid var(--vg-border-strong);color:var(--vg-text-strong);padding:6px;border-radius:6px;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .2s;" onmouseover="this.style.background='var(--vg-sidebar)'" onmouseout="this.style.background='transparent'">Reschedule</button>
                                    <button style="flex:1;background:transparent;border:1px solid rgba(244,63,94,.4);color:#f43f5e;padding:6px;border-radius:6px;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .2s;" onmouseover="this.style.background='rgba(244,63,94,.1)'" onmouseout="this.style.background='transparent'">Cancel</button>
                                </div>

                                @if($isTrainer)
                                    <form method="POST" action="{{ route('bookings.update', $booking->id) }}" style="margin-top:10px;">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" style="width:100%;background:var(--vg-panel-strong);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:6px;border-radius:6px;font-size:.75rem;">
                                            <option value="confirmed" selected>Update Status: Confirmed</option>
                                            <option value="completed">Mark Completed</option>
                                            <option value="cancelled">Mark Cancelled</option>
                                        </select>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align:center;padding:4rem 1rem;background:var(--vg-panel);border:1px dashed var(--vg-border-strong);border-radius:20px;">
                    <div style="font-size:3rem;margin-bottom:1rem;opacity:.5;">📅</div>
                    <h3 style="color:var(--vg-text-strong);font-size:1.2rem;font-weight:700;margin-bottom:.5rem;">No upcoming sessions</h3>
                    <p style="color:var(--vg-text-muted);font-size:.9rem;margin-bottom:1.5rem;">
                        {{ $isTrainer ? 'You have no confirmed bookings right now.' : 'Ready to train? Book a session to get started.' }}
                    </p>
                    @if(!$isTrainer)
                        <a href="{{ route('trainee.trainers') }}" style="display:inline-block;background:var(--vg-accent-soft);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:10px 24px;border-radius:10px;font-size:.85rem;font-weight:600;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--vg-accent-glow)'" onmouseout="this.style.background='var(--vg-accent-soft)'">Browse Trainers</a>
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
                                <p style="font-size:.9rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:2px;">{{ $partnerName }}</p>
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

    {{-- Summary Panel (Right Sidebar) --}}
    <div class="summary-panel" style="width:300px;flex-shrink:0;">
        @if(!$isTrainer)
            <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.6rem;position:sticky;top:2rem;">
                <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.5rem;">📊 Monthly Summary</h2>
                
                <div style="margin-bottom:1.5rem;">
                    <p style="font-size:.75rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Sessions Completed</p>
                    <p style="font-size:2rem;font-weight:900;color:var(--vg-text-strong);line-height:1;">{{ $totalSessionsCompleted }}</p>
                </div>
                
                <div style="margin-bottom:1.5rem;">
                    <p style="font-size:.75rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Total Spent</p>
                    <p style="font-size:2rem;font-weight:900;color:#10b981;line-height:1;">₹{{ number_format($totalSpentThisMonth) }}</p>
                    <p style="font-size:.7rem;color:var(--vg-text-faint);margin-top:4px;">Current month ({{ now()->format('F') }})</p>
                </div>

                <div style="background:var(--vg-sidebar);border:1px solid var(--vg-border-strong);border-radius:12px;padding:1rem;text-align:center;margin-bottom:1rem;">
                    <div style="font-size:1.5rem;margin-bottom:.5rem;">💡</div>
                    <p style="font-size:.75rem;color:var(--vg-text-muted);line-height:1.4;">Booking sessions regularly helps maintain consistency and achieve your goals faster!</p>
                </div>

                <a href="{{ route('music.index') }}" style="display:flex;align-items:center;justify-content:center;gap:8px;background:var(--vg-gradient);color:#fff;padding:12px;border-radius:12px;font-size:.85rem;font-weight:700;text-decoration:none;box-shadow:0 8px 20px var(--vg-accent-glow);transition:all .2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    <i data-lucide="music" style="width:16px;height:16px;"></i> Workout Music
                </a>
            </div>
        @else
            <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.6rem;position:sticky;top:2rem;">
                <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.5rem;">👨‍🏫 Trainer Tips</h2>
                <ul style="list-style:none;padding:0;margin:0;font-size:.8rem;color:var(--vg-text-muted);display:flex;flex-direction:column;gap:12px;">
                    <li style="display:flex;gap:8px;"><span style="color:var(--vg-accent);">•</span> Keep your availability updated.</li>
                    <li style="display:flex;gap:8px;"><span style="color:var(--vg-accent);">•</span> Join video sessions 5 mins early.</li>
                    <li style="display:flex;gap:8px;"><span style="color:var(--vg-accent);">•</span> Mark sessions as "Completed" right after finishing them.</li>
                </ul>
            </div>
        @endif
    </div>
</div>

<script>
    function switchTab(tabName) {
        // Update Buttons
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById('tab-' + tabName).classList.add('active');
        
        // Update Content
        document.getElementById('content-upcoming').style.display = 'none';
        document.getElementById('content-past').style.display = 'none';
        document.getElementById('content-cancelled').style.display = 'none';
        
        document.getElementById('content-' + tabName).style.display = 'block';
    }
</script>
@endsection
