@extends('layouts.app')

@section('title', 'Progress')

@section('content')
@php
    $latestDate = optional(optional($latest)->date)->format('M d, Y');
    $change = function ($field) use ($latest, $previous) {
        if (!$latest || !$previous || $latest->{$field} === null || $previous->{$field} === null) {
            return null;
        }

        return round((float) $latest->{$field} - (float) $previous->{$field}, 1);
    };
    $weightChange = $change('weight');
    $bodyFatChange = $change('body_fat_percentage');
    $muscleChange = $change('muscle_mass');
    $weights = $history->pluck('weight')->filter(fn ($value) => $value !== null)->values();
    $maxWeight = $weights->count() ? max($weights->max(), 1) : 1;
@endphp

<style>
    .progress-shell{max-width:1280px;margin:0 auto;}
    .progress-hero{display:flex;align-items:flex-end;justify-content:space-between;gap:1.5rem;margin-bottom:2rem;}
    .progress-title{font-size:1.9rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;letter-spacing:-.02em;}
    .progress-sub{color:var(--vg-text-muted);font-size:.88rem;}
    .progress-chip{background:var(--vg-accent-soft);border:1px solid var(--vg-border);color:var(--vg-text-strong);border-radius:999px;padding:8px 14px;font-size:.78rem;font-weight:700;white-space:nowrap;}
    .progress-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.1rem;margin-bottom:1.5rem;}
    .progress-card{background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.35rem;box-shadow:0 18px 36px rgba(0,0,0,.18);transition:transform .28s ease,border-color .28s ease,box-shadow .28s ease;}
    .progress-card:hover{transform:translateY(-4px);border-color:var(--vg-border-strong);box-shadow:0 24px 44px rgba(0,0,0,.24);}
    .metric-label{color:var(--vg-text-muted);font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.45rem;}
    .metric-value{font-size:2.25rem;font-weight:900;line-height:1;color:var(--vg-text-strong);}
    .metric-unit{font-size:.95rem;color:var(--vg-text-muted);font-weight:700;margin-left:3px;}
    .metric-delta{display:inline-flex;margin-top:.85rem;border-radius:999px;padding:5px 10px;font-size:.72rem;font-weight:800;}
    .delta-good{background:rgba(16,185,129,.14);color:#6ee7b7;border:1px solid rgba(16,185,129,.26);}
    .delta-watch{background:rgba(245,158,11,.14);color:#fbbf24;border:1px solid rgba(245,158,11,.26);}
    .progress-main{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(320px,.85fr);gap:1.5rem;}
    .panel{background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:22px;padding:1.5rem;box-shadow:0 18px 36px rgba(0,0,0,.18);}
    .panel-title{font-size:1rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:1.2rem;}
    .trend-bars{height:210px;display:flex;align-items:flex-end;gap:10px;padding-top:1.3rem;}
    .trend-bar-wrap{flex:1;min-width:20px;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;gap:8px;}
    .trend-bar{width:100%;max-width:38px;border-radius:8px 8px 3px 3px;background:linear-gradient(180deg,#6ee7b7,#8b5cf6);box-shadow:0 8px 20px rgba(139,92,246,.28);transform-origin:bottom;animation:barGrow .8s cubic-bezier(.23,1,.32,1) both;transition:filter .2s ease,transform .2s ease;}
    .trend-bar:hover{filter:brightness(1.14);transform:scaleY(1.03);}
    .trend-date{font-size:.65rem;color:var(--vg-text-muted);white-space:nowrap;}
    .progress-form{display:grid;gap:.9rem;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:.8rem;}
    .progress-label{display:block;color:var(--vg-text-muted);font-size:.72rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;}
    .progress-input{width:100%;background:var(--vg-sidebar);border:1px solid var(--vg-border);border-radius:12px;color:var(--vg-text-strong);padding:11px 12px;outline:none;transition:border-color .2s ease,box-shadow .2s ease,transform .2s ease;}
    .progress-input:focus{border-color:var(--vg-accent);box-shadow:0 0 0 3px var(--vg-accent-soft);transform:translateY(-1px);}
    .save-btn{background:var(--vg-gradient);border:none;color:#fff;border-radius:13px;padding:12px 18px;font-size:.92rem;font-weight:800;cursor:pointer;box-shadow:0 12px 28px rgba(139,92,246,.32);transition:transform .24s ease,box-shadow .24s ease;}
    .save-btn:hover{transform:translateY(-2px);box-shadow:0 18px 36px rgba(139,92,246,.42);}
    .history-list{display:grid;gap:.7rem;margin-top:1rem;max-height:300px;overflow:auto;padding-right:4px;}
    .history-item{display:grid;grid-template-columns:90px repeat(3,1fr);gap:.7rem;align-items:center;background:var(--vg-sidebar);border:1px solid var(--vg-border);border-radius:14px;padding:.85rem;transition:transform .2s ease,border-color .2s ease;}
    .history-item:hover{transform:translateX(3px);border-color:var(--vg-border-strong);}
    .history-date{font-size:.75rem;font-weight:800;color:var(--vg-text-strong);}
    .history-stat{font-size:.75rem;color:var(--vg-text-muted);}
    .empty-progress{height:210px;display:flex;align-items:center;justify-content:center;text-align:center;color:var(--vg-text-muted);font-size:.88rem;border:1px dashed var(--vg-border);border-radius:16px;}
    .success-note{background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.28);color:#6ee7b7;border-radius:14px;padding:.85rem 1rem;margin-bottom:1.2rem;font-size:.85rem;font-weight:700;animation:fadeSlide .4s ease both;}
    .stagger-in{animation:fadeSlide .55s cubic-bezier(.23,1,.32,1) both;}
    .delay-1{animation-delay:.05s}.delay-2{animation-delay:.1s}.delay-3{animation-delay:.15s}.delay-4{animation-delay:.2s}
    @keyframes fadeSlide{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    @keyframes barGrow{from{transform:scaleY(.08);opacity:.45}to{transform:scaleY(1);opacity:1}}
    @media(max-width:900px){.progress-hero{align-items:flex-start;flex-direction:column}.progress-grid,.progress-main{grid-template-columns:1fr}.form-row{grid-template-columns:1fr}.history-item{grid-template-columns:1fr 1fr}.progress-chip{white-space:normal}}
</style>

<div class="progress-shell">
    <div class="progress-hero stagger-in">
        <div>
            <h1 class="progress-title">Progress Tracking 🎯</h1>
            <p class="progress-sub">Log body metrics, watch trends move, and keep your dashboard current.</p>
        </div>
        <div class="progress-chip">
            {{ $latest ? 'Last update: ' . $latestDate : 'No entries yet' }}
        </div>
    </div>

    @if(session('success'))
        <div class="success-note">{{ session('success') }}</div>
    @endif

    <div class="progress-grid">
        <div class="progress-card stagger-in delay-1">
            <p class="metric-label">Weight</p>
            <div class="metric-value">{{ $latest->weight ?? ($user->weight ?? '--') }}<span class="metric-unit">kg</span></div>
            @if($weightChange !== null)
                <span class="metric-delta {{ $weightChange <= 0 ? 'delta-good' : 'delta-watch' }}">{{ $weightChange > 0 ? '+' : '' }}{{ $weightChange }} kg</span>
            @endif
        </div>

        <div class="progress-card stagger-in delay-2">
            <p class="metric-label">Body Fat</p>
            <div class="metric-value">{{ $latest->body_fat_percentage ?? '--' }}<span class="metric-unit">%</span></div>
            @if($bodyFatChange !== null)
                <span class="metric-delta {{ $bodyFatChange <= 0 ? 'delta-good' : 'delta-watch' }}">{{ $bodyFatChange > 0 ? '+' : '' }}{{ $bodyFatChange }}%</span>
            @endif
        </div>

        <div class="progress-card stagger-in delay-3">
            <p class="metric-label">Muscle Mass</p>
            <div class="metric-value">{{ $latest->muscle_mass ?? '--' }}<span class="metric-unit">%</span></div>
            @if($muscleChange !== null)
                <span class="metric-delta {{ $muscleChange >= 0 ? 'delta-good' : 'delta-watch' }}">{{ $muscleChange > 0 ? '+' : '' }}{{ $muscleChange }}%</span>
            @endif
        </div>
    </div>

    <div class="progress-main">
        <div class="panel stagger-in delay-2">
            <h2 class="panel-title">Weight Trend</h2>
            @if($history->count() && $weights->count())
                <div class="trend-bars">
                    @foreach($history as $entry)
                        @php
                            $entryWeight = $entry->weight ?: 0;
                            $barHeight = $entryWeight ? max(10, ($entryWeight / $maxWeight) * 100) : 0;
                        @endphp
                        <div class="trend-bar-wrap">
                            <div class="trend-bar" title="{{ $entryWeight ?: 'No weight' }} kg" style="height:{{ $barHeight }}%;animation-delay:{{ $loop->index * 45 }}ms;"></div>
                            <span class="trend-date">{{ optional($entry->date)->format('M d') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-progress">Add your first progress entry to start the trend.</div>
            @endif

            @if($metrics->count())
                <div class="history-list">
                    @foreach($metrics->take(6) as $entry)
                        <div class="history-item">
                            <div class="history-date">{{ optional($entry->date)->format('M d') }}</div>
                            <div class="history-stat">{{ $entry->weight ?? '--' }} kg</div>
                            <div class="history-stat">{{ $entry->body_fat_percentage ?? '--' }}% fat</div>
                            <div class="history-stat">{{ $entry->muscle_mass ?? '--' }}% muscle</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="panel stagger-in delay-3">
            <h2 class="panel-title">Update Metrics</h2>
            <form method="POST" action="{{ route('progress.store') }}" class="progress-form">
                @csrf

                <div>
                    <label class="progress-label">Date</label>
                    <input class="progress-input" type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required>
                    @error('date')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                </div>

                <div class="form-row">
                    <div>
                        <label class="progress-label">Weight (kg)</label>
                        <input class="progress-input" type="number" step="0.1" name="weight" value="{{ old('weight', $latest->weight ?? $user->weight) }}">
                        @error('weight')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="progress-label">Body Fat (%)</label>
                        <input class="progress-input" type="number" step="0.1" name="body_fat_percentage" value="{{ old('body_fat_percentage', $latest->body_fat_percentage ?? '') }}">
                        @error('body_fat_percentage')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label class="progress-label">Muscle Mass (%)</label>
                        <input class="progress-input" type="number" step="0.1" name="muscle_mass" value="{{ old('muscle_mass', $latest->muscle_mass ?? '') }}">
                        @error('muscle_mass')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="progress-label">Waist (cm)</label>
                        <input class="progress-input" type="number" step="0.1" name="waist" value="{{ old('waist', $latest->waist ?? '') }}">
                        @error('waist')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label class="progress-label">Chest (cm)</label>
                        <input class="progress-input" type="number" step="0.1" name="chest" value="{{ old('chest', $latest->chest ?? '') }}">
                        @error('chest')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="progress-label">Hips (cm)</label>
                        <input class="progress-input" type="number" step="0.1" name="hips" value="{{ old('hips', $latest->hips ?? '') }}">
                        @error('hips')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="progress-label">Notes</label>
                    <textarea class="progress-input" name="notes" rows="3" placeholder="Energy, soreness, measurements context...">{{ old('notes', $latest->notes ?? '') }}</textarea>
                    @error('notes')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                </div>

                <button class="save-btn" type="submit">Save Progress</button>
            </form>
        </div>
    </div>
</div>
@endsection
