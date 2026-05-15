@extends('layouts.app')

@section('title', 'Video Session')

@section('content')
<div style="height:calc(100vh - 120px);display:flex;flex-direction:column;background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;overflow:hidden;" class="fade-in-up">
    <div style="background:var(--vg-gradient);padding:1.2rem 2rem;color:#fff;">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
            <div>
                <h1 style="font-size:1.2rem;font-weight:800;margin:0;">
                    Video Session with 
                    @if(Auth::id() == $booking->trainer_id)
                        {{ $booking->trainee->name ?? 'Trainee' }}
                    @else
                        {{ $booking->trainer->name ?? 'Trainer' }}
                    @endif
                </h1>
                <p style="font-size:.8rem;opacity:.8;margin:4px 0 0;">{{ \Carbon\Carbon::parse($booking->session_date)->format('F d, Y • h:i A') }}</p>
            </div>
            <div style="display:flex;gap:10px;align-items:center;">
                {{-- Music Button --}}
                <a href="{{ route('music.index') }}" target="_blank" style="background:rgba(255,255,255,.2);color:#fff;padding:8px 16px;border-radius:10px;font-size:.8rem;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:6px;transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.2)'">
                    <i data-lucide="music" style="width:16px;height:16px;"></i> Workout Music
                </a>

                @if(Auth::id() == $booking->trainer_id && !$booking->meeting_ended)
                    <button onclick="endMeeting()" style="background:#ef4444;color:#fff;padding:8px 16px;border-radius:10px;font-size:.8rem;font-weight:700;border:none;cursor:pointer;transition:all .2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                        End Session
                    </button>
                @endif
                <a href="{{ route('bookings.index') }}" style="background:rgba(0,0,0,.3);color:#fff;padding:8px 16px;border-radius:10px;font-size:.8rem;font-weight:700;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='rgba(0,0,0,.4)'" onmouseout="this.style.background='rgba(0,0,0,.3)'">
                    Exit
                </a>
            </div>
        </div>
    </div>
    
    <div style="flex:1;background:#000;">
        <iframe 
            src="{{ $meetingLink }}"
            allow="camera; microphone; fullscreen; display-capture"
            style="width:100%;height:100%;border:0;"
            allowfullscreen
        ></iframe>
    </div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
    function endMeeting() {
        if (confirm('Are you sure you want to end this session?')) {
            fetch('{{ route("video-call.end", $booking->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                window.location.href = '{{ route("bookings.index") }}';
            });
        }
    }
    
    window.addEventListener('DOMContentLoaded', () => {
        if(typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endsection