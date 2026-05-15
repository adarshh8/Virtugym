@extends('layouts.app')

@section('title', 'Progress Report')

@section('content')
<div style="max-width:1100px;margin:0 auto;" id="printableReport">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem;" class="fade-in-up">
        <div>
            <h1 style="font-size:2rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
                Performance Report 📊
            </h1>
            <p style="color:var(--vg-text-muted);font-size:.9rem;">Report for {{ $user->name }} • {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</p>
        </div>
        <button onclick="window.print()" style="background:rgba(255,255,255,.05);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:.6rem 1.2rem;border-radius:12px;font-size:.85rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;">
            <i data-lucide="printer" style="width:16px;"></i> Print Report
        </button>
    </div>

    <!-- Summary Cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.5rem;margin-bottom:2rem;" class="fade-in-up delay-1">
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;text-align:center;">
            <p style="font-size:.75rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.5rem;">Workouts Done</p>
            <h3 style="font-size:2rem;font-weight:900;color:var(--vg-text-strong);">{{ $workouts->where('completed_at', '!=', null)->count() }}</h3>
        </div>
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;text-align:center;">
            <p style="font-size:.75rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.5rem;">Total Volume</p>
            <h3 style="font-size:2rem;font-weight:900;color:var(--vg-text-strong);">{{ number_format($totalVolume) }}kg</h3>
        </div>
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;text-align:center;">
            <p style="font-size:.75rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.5rem;">Avg Hydration</p>
            <h3 style="font-size:2rem;font-weight:900;color:var(--vg-text-strong);">{{ number_format($avgWater) }}ml</h3>
        </div>
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;text-align:center;">
            <p style="font-size:.75rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.5rem;">Weight Change</p>
            @php 
                $startWeight = $metrics->first()->weight ?? $user->weight ?? 0;
                $endWeight = $metrics->last()->weight ?? $user->weight ?? 0;
                $diff = $endWeight - $startWeight;
            @endphp
            <h3 style="font-size:2rem;font-weight:900;color:{{ $diff <= 0 ? '#10b981' : '#f43f5e' }};">
                {{ ($diff > 0 ? '+' : '') . $diff }}kg
            </h3>
        </div>
    </div>

    <!-- Charts/Details -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(450px,1fr));gap:1.5rem;margin-bottom:2rem;" class="fade-in-up delay-2">
        <!-- Workout History -->
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;">
            <h3 style="font-size:1.1rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:1.5rem;">Recent Workouts</h3>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                @foreach($workouts->take(5) as $workout)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:1rem;background:rgba(255,255,255,.02);border-radius:16px;border:1px solid rgba(255,255,255,.05);">
                        <div>
                            <p style="font-size:.9rem;font-weight:700;color:var(--vg-text-strong);">{{ $workout->name }}</p>
                            <p style="font-size:.75rem;color:var(--vg-text-muted);">{{ Carbon\Carbon::parse($workout->created_at)->format('M d, Y') }}</p>
                        </div>
                        <span style="padding:4px 10px;border-radius:50px;font-size:.7rem;font-weight:700;{{ $workout->completed_at ? 'background:rgba(16,185,129,.15);color:#6ee7b7;' : 'background:rgba(255,255,255,.05);color:var(--vg-text-muted);' }}">
                            {{ $workout->completed_at ? 'Completed' : 'Pending' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Body Metrics -->
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;">
            <h3 style="font-size:1.1rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:1.5rem;">Weight Trend</h3>
            <div style="height:250px;display:flex;align-items:flex-end;gap:10px;padding-bottom:2rem;">
                @foreach($metrics->take(-10) as $m)
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:.5rem;height:100%;">
                        @php $h = ($m->weight / 150) * 100; @endphp
                        <div style="width:100%;background:var(--vg-accent);border-radius:4px;height:{{ $h }}%;opacity:.6;min-height:2px;"></div>
                        <span style="font-size:.6rem;color:var(--vg-text-muted);transform:rotate(-45deg);white-space:nowrap;">{{ Carbon\Carbon::parse($m->created_at)->format('m/d') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .nav-dark, .sidebar, button, #globalMusicDock { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
    body { background: white !important; color: black !important; }
    #printableReport { max-width: 100% !important; }
    #printableReport h1 { color: black !important; background: none !important; -webkit-background-clip: initial !important; }
    .fade-in-up { opacity: 1 !important; transform: none !important; animation: none !important; }
    [style*="background:var(--vg-panel)"] { background: #f9f9f9 !important; border: 1px solid #eee !important; }
    [style*="color:var(--vg-text-strong)"] { color: #111 !important; }
    [style*="color:var(--vg-text-muted)"] { color: #666 !important; }
}
</style>
@endsection
