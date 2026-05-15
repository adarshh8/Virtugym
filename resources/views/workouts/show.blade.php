@extends('layouts.app')

@section('title', 'Workout Details')

@section('content')
<div style="max-width:1080px;margin:0 auto;">
    <div style="margin-bottom:1.5rem;" class="fade-in-up">
        <a href="{{ route('workouts.index') }}" style="color:#c4b5fd;text-decoration:none;font-size:.85rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;transition:color .2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#c4b5fd'">
            ← Back to Workouts
        </a>
    </div>

    <div style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;overflow:hidden;margin-bottom:2rem;" class="fade-in-up delay-1">
        {{-- Header --}}
        <div style="background:linear-gradient(135deg,rgba(139,92,246,.15),rgba(236,72,153,.1));border-bottom:1px solid rgba(139,92,246,.12);padding:2rem;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <h1 style="font-size:2rem;font-weight:900;color:#fff;margin-bottom:.5rem;">{{ $workout->title }}</h1>
                    <div style="display:flex;gap:.8rem;align-items:center;flex-wrap:wrap;">
                        <span style="background:rgba(255,255,255,.1);padding:4px 12px;border-radius:8px;font-size:.8rem;color:rgba(255,255,255,.8);">🏷️ {{ $workout->type }}</span>
                        <span style="background:rgba(255,255,255,.1);padding:4px 12px;border-radius:8px;font-size:.8rem;color:rgba(255,255,255,.8);">📊 {{ $workout->difficulty }}</span>
                        @if($workout->duration_minutes)
                            <span style="background:rgba(255,255,255,.1);padding:4px 12px;border-radius:8px;font-size:.8rem;color:rgba(255,255,255,.8);">⏱️ {{ $workout->duration_minutes }} mins</span>
                        @endif
                        <a href="{{ route('music.index') }}" target="_blank" style="background:rgba(236,72,153,.2);padding:4px 12px;border-radius:8px;font-size:.8rem;color:#f9a8d4;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:6px;border:1px solid rgba(236,72,153,.3);transition:all .2s;" onmouseover="this.style.background='rgba(236,72,153,.3)'" onmouseout="this.style.background='rgba(236,72,153,.2)'">
                            🎵 Music Player
                        </a>
                    </div>
                </div>
                @if($workout->completed_at)
                    <div style="background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);color:#6ee7b7;padding:8px 16px;border-radius:12px;font-weight:800;font-size:.9rem;display:flex;align-items:center;gap:8px;">
                        <span>✓</span> Completed on {{ $workout->completed_at->format('M d, Y') }}
                    </div>
                @else
                    <div style="background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fcd34d;padding:8px 16px;border-radius:12px;font-weight:800;font-size:.9rem;display:flex;align-items:center;gap:8px;">
                        <span>⏳</span> Pending
                    </div>
                @endif
            </div>
        </div>

        {{-- Exercises List --}}
        <div style="padding:2rem;">
            <h2 style="font-size:1.2rem;font-weight:800;color:#e2d9f3;margin-bottom:1.5rem;display:flex;align-items:center;gap:10px;">
                <span style="background:rgba(139,92,246,.2);color:#c4b5fd;width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;">💪</span>
                Exercises Plan
            </h2>
            
            @if(count($exercises) > 0)
                <div style="display:flex;flex-direction:column;gap:1rem;">
                    @foreach($exercises as $index => $item)
                        <div style="background:rgba(0,0,0,.2);border:1px solid rgba(139,92,246,.15);border-radius:16px;padding:1.2rem;display:flex;align-items:center;gap:1.5rem;">
                            <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:1.2rem;flex-shrink:0;">
                                {{ $index + 1 }}
                            </div>
                            <div style="flex:1;">
                                <h3 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:4px;">{{ $item->exercise->name }}</h3>
                                <p style="font-size:.8rem;color:rgba(255,255,255,.5);">Muscle: {{ $item->exercise->muscle_group }}</p>
                            </div>
                            <div style="display:flex;gap:1.5rem;text-align:center;">
                                <div>
                                    <p style="font-size:.7rem;font-weight:800;color:rgba(196,181,253,.6);letter-spacing:.05em;margin-bottom:4px;">SETS</p>
                                    <p style="font-size:1.2rem;font-weight:900;color:#e2d9f3;">{{ $item->sets }}</p>
                                </div>
                                <div>
                                    <p style="font-size:.7rem;font-weight:800;color:rgba(196,181,253,.6);letter-spacing:.05em;margin-bottom:4px;">REPS</p>
                                    <p style="font-size:1.2rem;font-weight:900;color:#e2d9f3;">{{ $item->reps }}</p>
                                </div>
                                @if($item->target_weight)
                                    <div>
                                        <p style="font-size:.7rem;font-weight:800;color:rgba(196,181,253,.6);letter-spacing:.05em;margin-bottom:4px;">TARGET WT</p>
                                        <p style="font-size:1.2rem;font-weight:900;color:#e2d9f3;">{{ $item->target_weight }}<span style="font-size:.8rem;opacity:.5;">kg</span></p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color:rgba(255,255,255,.3);font-size:.9rem;">No exercises found in this plan.</p>
            @endif
        </div>
        
        {{-- Actions --}}
        @if(!$workout->completed_at && Auth::user()->role === 'trainee')
            <div style="border-top:1px solid rgba(139,92,246,.12);padding:2rem;background:rgba(16,185,129,.03);">
                <form action="{{ route('workouts.complete', $workout->id) }}" method="POST">
                    @csrf
                    <h3 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1rem;">Complete Workout</h3>
                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block;font-size:.8rem;font-weight:700;color:rgba(255,255,255,.6);margin-bottom:8px;">How did it go? (Optional Notes)</label>
                        <textarea name="notes" rows="3" placeholder="I crushed it today! Felt great on the bench press..."
                                  style="width:100%;padding:12px;background:rgba(8,8,26,.8);border:1px solid rgba(16,185,129,.3);border-radius:12px;color:#fff;font-size:.9rem;outline:none;resize:vertical;"></textarea>
                    </div>
                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block;font-size:.8rem;font-weight:700;color:rgba(255,255,255,.6);margin-bottom:8px;">Rate Difficulty (1-10)</label>
                        <input type="number" name="rating" min="1" max="10" placeholder="e.g. 7"
                               style="width:100%;max-width:200px;padding:12px;background:rgba(8,8,26,.8);border:1px solid rgba(16,185,129,.3);border-radius:12px;color:#fff;font-size:.9rem;outline:none;">
                    </div>
                    <button type="submit" 
                            style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:12px;padding:14px 28px;font-size:1rem;font-weight:800;cursor:pointer;box-shadow:0 8px 20px rgba(16,185,129,.3);transition:all .3s;"
                            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 14px 30px rgba(16,185,129,.4)'"
                            onmouseout="this.style.transform='';this.style.boxShadow='0 8px 20px rgba(16,185,129,.3)'">
                        Mark as Completed 🏆
                    </button>
                </form>
            </div>
        @endif
        
        @if($workout->completed_at)
            <div style="border-top:1px solid rgba(139,92,246,.12);padding:2rem;background:rgba(255,255,255,.02);">
                <h3 style="font-size:1rem;font-weight:800;color:#c4b5fd;margin-bottom:1rem;">Completion Details</h3>
                @if($workout->notes)
                    <div style="background:rgba(0,0,0,.3);border-left:4px solid #10b981;padding:1rem 1.5rem;border-radius:0 12px 12px 0;margin-bottom:1rem;">
                        <p style="font-size:.9rem;color:rgba(255,255,255,.8);font-style:italic;">"{{ $workout->notes }}"</p>
                    </div>
                @endif
                @if($workout->rating)
                    <p style="font-size:.9rem;color:rgba(255,255,255,.6);">Difficulty Rating: <strong style="color:#fff;">{{ $workout->rating }}/10</strong></p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
