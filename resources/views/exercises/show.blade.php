@extends('layouts.app')

@section('title', $exercise->name)

@section('content')
<div style="max-width:960px;margin:0 auto;">
    <div style="margin-bottom:1.5rem;" class="fade-in-up">
        <a href="{{ route('exercises.index') }}" style="color:#c4b5fd;text-decoration:none;font-size:.85rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;transition:color .2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#c4b5fd'">
            ← Back to Exercises
        </a>
    </div>
    
    <div style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;overflow:hidden;" class="fade-in-up delay-1">
        {{-- Header --}}
        <div style="background:linear-gradient(135deg,rgba(139,92,246,.15),rgba(236,72,153,.1));border-bottom:1px solid rgba(139,92,246,.12);padding:2.5rem 2rem;">
            <div style="font-size:4rem;margin-bottom:1rem;text-shadow:0 0 20px rgba(139,92,246,.4);">
                @switch($exercise->muscle_group)
                    @case('Chest') 💪 @break
                    @case('Back') 🏋️ @break
                    @case('Legs') 🦵 @break
                    @case('Shoulders') 🎯 @break
                    @default 🏃
                @endswitch
            </div>
            <h1 style="font-size:2.2rem;font-weight:900;color:#fff;margin-bottom:1rem;">{{ $exercise->name }}</h1>
            <div style="display:flex;flex-wrap:wrap;gap:.8rem;">
                <span style="background:rgba(255,255,255,.1);padding:4px 12px;border-radius:8px;font-size:.8rem;font-weight:600;color:#e2d9f3;">Muscle: {{ $exercise->muscle_group }}</span>
                <span style="background:rgba(255,255,255,.1);padding:4px 12px;border-radius:8px;font-size:.8rem;font-weight:600;color:#e2d9f3;">Equipment: {{ $exercise->equipment }}</span>
                <span style="background:rgba(255,255,255,.1);padding:4px 12px;border-radius:8px;font-size:.8rem;font-weight:600;color:#e2d9f3;">Difficulty: {{ $exercise->difficulty }}</span>
            </div>
        </div>
        
        <div style="padding:2.5rem 2rem;">
            <div style="margin-bottom:2.5rem;">
                <h2 style="font-size:1.2rem;font-weight:800;color:#c4b5fd;margin-bottom:.8rem;display:flex;align-items:center;gap:8px;">
                    📝 Instructions
                </h2>
                <div style="background:rgba(0,0,0,.2);border-radius:16px;padding:1.5rem;border:1px solid rgba(255,255,255,.05);">
                    <p style="font-size:.95rem;color:rgba(255,255,255,.7);line-height:1.7;">{{ $exercise->instructions }}</p>
                </div>
            </div>
            
            @if($exercise->tips)
            <div style="margin-bottom:2.5rem;">
                <h2 style="font-size:1.2rem;font-weight:800;color:#fcd34d;margin-bottom:.8rem;display:flex;align-items:center;gap:8px;">
                    💡 Pro Tips
                </h2>
                <div style="background:rgba(245,158,11,.05);border-radius:16px;padding:1.5rem;border:1px solid rgba(245,158,11,.2);">
                    <p style="font-size:.95rem;color:rgba(255,255,255,.7);line-height:1.7;">{{ $exercise->tips }}</p>
                </div>
            </div>
            @endif

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;margin-bottom:2.5rem;">
                @if($exercise->benefits)
                <div>
                    <h2 style="font-size:1.2rem;font-weight:800;color:#10b981;margin-bottom:.8rem;display:flex;align-items:center;gap:8px;">
                        🌟 Benefits
                    </h2>
                    <div style="background:rgba(16,185,129,.05);border-radius:16px;padding:1.5rem;border:1px solid rgba(16,185,129,.2);height:calc(100% - 2.5rem);">
                        <p style="font-size:.95rem;color:rgba(255,255,255,.7);line-height:1.7;">{{ $exercise->benefits }}</p>
                    </div>
                </div>
                @endif

                @if($exercise->precautions)
                <div>
                    <h2 style="font-size:1.2rem;font-weight:800;color:#f43f5e;margin-bottom:.8rem;display:flex;align-items:center;gap:8px;">
                        ⚠️ Precautions
                    </h2>
                    <div style="background:rgba(244,63,94,.05);border-radius:16px;padding:1.5rem;border:1px solid rgba(244,63,94,.2);height:calc(100% - 2.5rem);">
                        <p style="font-size:.95rem;color:rgba(255,255,255,.7);line-height:1.7;">{{ $exercise->precautions }}</p>
                    </div>
                </div>
                @endif
            </div>
            
            @if(Auth::user()->role === 'trainer')
                <div style="background:rgba(139,92,246,.08);border:1px solid rgba(139,92,246,.2);border-radius:16px;padding:2rem;text-align:center;">
                    <h3 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:.5rem;">Ready to add this exercise?</h3>
                    <p style="color:rgba(255,255,255,.5);font-size:.9rem;margin-bottom:1.5rem;">Add this exercise to a client's workout plan</p>
                    <a href="{{ route('workouts.create') }}" 
                       style="display:inline-block;background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;text-decoration:none;padding:12px 24px;border-radius:12px;font-size:.9rem;font-weight:700;box-shadow:0 8px 20px rgba(139,92,246,.35);transition:all .3s;"
                       onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 14px 30px rgba(139,92,246,.5)'"
                       onmouseout="this.style.transform='';this.style.boxShadow='0 8px 20px rgba(139,92,246,.35)'">
                        Add to Workout Plan →
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection