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
                            
<<<<<<< Updated upstream
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
=======
                            {{-- Info Section --}}
                            <div style="display:flex;gap:1rem;align-items:flex-start;">
                                <div style="width:50px;height:50px;border-radius:50%;background:{{ $avatarColor }};display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                                        <h3 style="font-size:1.1rem;font-weight:700;color:var(--vg-text-strong);">{{ $partnerName }}</h3>
                                        @if(!$isTrainer)
                                            <span style="font-size:.65rem;background:rgba(139,92,246,.15);color:#a78bfa;border:1px solid rgba(139,92,246,.3);padding:2px 8px;border-radius:50px;font-weight:600;">{{ optional($partner)->specialization ?? 'Personal Training' }}</span>
                                            <span style="font-size:.65rem;background:rgba(245,158,11,.15);color:#fbbf24;border:1px solid rgba(245,158,11,.3);padding:2px 8px;border-radius:50px;font-weight:600;">★ {{ optional($partner)->rating ?? '5.0' }}</span>
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
                                
                                <a href="{{ route('video-call.join', $booking->id) }}" style="display:inline-block;width:100%;text-align:center;background:var(--vg-accent);color:var(--vg-text-strong);padding:8px 16px;border-radius:8px;font-size:.8rem;font-weight:700;text-decoration:none;margin-bottom:8px;transition:all .2s;" onmouseover="this.style.background='var(--vg-accent-glow)'" onmouseout="this.style.background='var(--vg-accent)'">
                                    🎥 Join Video Session
                                </a>

                                @if(!$canJoin)
                                    @php
                                        $d = \Carbon\Carbon::parse($booking->session_date);
                                        $joinAt = $d->copy()->subMinutes(15);
                                        $nowDate = now();
                                        $diff = $nowDate->diff($joinAt);
                                        $countdown = '';
                                        if ($joinAt->diffInDays($nowDate) > 0) {
                                            $countdown = 'Opens in ' . $joinAt->diffInDays($nowDate) . ' days';
                                        } else {
                                            $countdown = 'Opens in ' . $diff->h . 'h ' . $diff->i . 'm';
                                        }
                                    @endphp
                                    <div style="width:100%;text-align:center;background:var(--vg-sidebar);border:1px solid var(--vg-border-strong);color:var(--vg-text-muted);padding:6px 10px;border-radius:8px;font-size:.72rem;font-weight:600;margin-bottom:8px;">
                                        ⏳ {{ $countdown }}
                                    </div>
                                @endif
                                
                                @if($isTrainer)
                                    <form method="POST" action="{{ route('bookings.update', $booking->id) }}" style="margin-top:10px;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" style="width:100%;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.32);color:#6ee7b7;padding:7px 10px;border-radius:8px;font-size:.75rem;font-weight:700;cursor:pointer;">Mark Completed</button>
>>>>>>> Stashed changes
                                    </form>

                                    <details style="margin-top:8px;text-align:left;">
                                        <summary style="list-style:none;cursor:pointer;width:100%;text-align:center;background:transparent;border:1px solid rgba(244,63,94,.4);color:#f43f5e;padding:7px 10px;border-radius:8px;font-size:.75rem;font-weight:700;">Cancel Session</summary>
                                        <form method="POST" action="{{ route('bookings.update', $booking->id) }}" style="margin-top:8px;background:var(--vg-sidebar);border:1px solid var(--vg-border);border-radius:10px;padding:10px;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <label style="display:block;font-size:.7rem;color:var(--vg-text-muted);font-weight:700;margin-bottom:5px;">Reason shared with trainee</label>
                                            <textarea name="cancellation_reason" rows="3" required minlength="5" maxlength="500" placeholder="Explain why this session is being cancelled..." style="width:100%;background:var(--vg-panel);border:1px solid var(--vg-border);color:var(--vg-text-strong);border-radius:8px;padding:8px;font-size:.75rem;resize:vertical;margin-bottom:8px;"></textarea>
                                            <button type="submit" style="width:100%;background:rgba(244,63,94,.14);border:1px solid rgba(244,63,94,.38);color:#fb7185;padding:7px 10px;border-radius:8px;font-size:.75rem;font-weight:700;cursor:pointer;">Confirm Cancellation</button>
                                        </form>
                                    </details>
                                @endif
<<<<<<< Updated upstream
                                
                            @elseif($booking->status == 'completed')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">✓ Completed</span>
                            @elseif($booking->status == 'cancelled')
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">✗ Cancelled</span>
=======
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
                            @if($booking->cancellation_reason)
                                <div style="background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.18);border-radius:10px;padding:10px;margin-bottom:12px;">
                                    <p style="font-size:.68rem;color:#fb7185;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Cancellation Reason</p>
                                    <p style="font-size:.8rem;color:var(--vg-text-strong);line-height:1.45;">{{ $booking->cancellation_reason }}</p>
                                </div>
                            @endif
                            @if(!$isTrainer && $partner)
                                <a href="{{ route('book.trainer.create', $partner->id) }}" style="display:inline-block;font-size:.75rem;color:var(--vg-accent);font-weight:600;text-decoration:none;">Rebook Session →</a>
>>>>>>> Stashed changes
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
