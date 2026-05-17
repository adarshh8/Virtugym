@extends('layouts.app')

@section('title', 'Water Intake')

@section('content')
<div style="max-width:1000px;margin:0 auto;">
    <div style="margin-bottom:2rem;" class="fade-in-up">
        <h1 style="font-size:1.8rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
            Hydration Tracker 💧
        </h1>
        <p style="color:var(--vg-text-muted);font-size:.9rem;">Stay hydrated to perform at your best</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;">
        <!-- Daily Goal -->
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:2rem;text-align:center;" class="fade-in-up delay-1">
            <div style="position:relative;width:180px;height:180px;margin:0 auto 1.5rem;">
                <svg viewBox="0 0 36 36" style="width:100%;height:100%;transform:rotate(-90deg);">
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="2.5" />
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--vg-accent)" stroke-width="2.5" stroke-dasharray="{{ $percentage }}, 100" stroke-linecap="round" style="transition: stroke-dasharray 0.5s ease;" />
                </svg>
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <span style="font-size:2.2rem;font-weight:900;color:var(--vg-text-strong);">{{ $percentage }}%</span>
                    <span style="font-size:.7rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.1em;">of daily goal</span>
                </div>
            </div>
            
            <h3 style="font-size:1.4rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:.5rem;">{{ $totalToday }}ml / {{ $goal }}ml</h3>
            <p style="color:var(--vg-text-muted);font-size:.85rem;margin-bottom:1.5rem;">{{ $goal - $totalToday > 0 ? 'Remaining: ' . ($goal - $totalToday) . 'ml' : 'Goal Achieved! 🎉' }}</p>

            <form action="{{ route('water.store') }}" method="POST" style="display:flex;flex-wrap:wrap;justify-content:center;gap:.75rem;">
                @csrf
                <button type="submit" name="amount_ml" value="250" style="background:rgba(255,255,255,.05);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:.6rem 1rem;border-radius:12px;font-size:.8rem;font-weight:700;cursor:pointer;transition:all .2s;">+250ml</button>
                <button type="submit" name="amount_ml" value="500" style="background:rgba(255,255,255,.05);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:.6rem 1rem;border-radius:12px;font-size:.8rem;font-weight:700;cursor:pointer;transition:all .2s;">+500ml</button>
                <button type="submit" name="amount_ml" value="750" style="background:rgba(255,255,255,.05);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:.6rem 1rem;border-radius:12px;font-size:.8rem;font-weight:700;cursor:pointer;transition:all .2s;">+750ml</button>
            </form>
        </div>

        <!-- Weekly History -->
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;" class="fade-in-up delay-2">
            <h3 style="font-size:1.1rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:1.5rem;">Weekly History</h3>
            <div style="display:flex;align-items:flex-end;justify-content:space-between;height:150px;padding-bottom:1.5rem;">
                @php $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']; @endphp
                @foreach($days as $day)
                    @php 
                        $val = $history->get($day, 0); 
                        $h = min(100, ($val / $goal) * 100);
                    @endphp
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:.5rem;">
                        <div style="width:20px;height:{{ max(4, $h) }}%;background:{{ $h >= 100 ? 'var(--vg-gradient)' : 'var(--vg-accent)' }};border-radius:6px;opacity:.8;transition: height 0.3s ease;"></div>
                        <span style="font-size:.65rem;color:var(--vg-text-muted);font-weight:700;">{{ $day }}</span>
                    </div>
                @endforeach
            </div>
            
            <div style="margin-top:1rem;border-top:1px solid var(--vg-border);padding-top:1rem;">
                <h4 style="font-size:.85rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1rem;">Today's Logs</h4>
                <div style="display:flex;flex-direction:column;gap:.5rem;max-height:200px;overflow-y:auto;">
                    @forelse($intakes as $intake)
                        <div style="display:flex;justify-content:space-between;background:rgba(255,255,255,.02);padding:.6rem 1rem;border-radius:10px;border:1px solid rgba(255,255,255,.05);">
                            <span style="font-size:.85rem;color:var(--vg-text-strong);font-weight:600;">{{ $intake->amount_ml }}ml</span>
                            <span style="font-size:.75rem;color:var(--vg-text-muted);">{{ $intake->created_at->format('H:i') }}</span>
                        </div>
                    @empty
                        <p style="text-align:center;color:var(--vg-text-faint);font-size:.8rem;padding:1rem;">No logs yet today.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
