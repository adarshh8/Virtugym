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
    $bmiChange = $change('bmi');
    
    $weights = $history->pluck('weight')->filter(fn ($value) => $value !== null)->values();
    $maxWeight = $weights->count() ? max($weights->max(), 1) : 1;
@endphp

<style>
    .progress-shell{max-width:1280px;margin:0 auto;}
    .progress-hero{display:flex;align-items:flex-end;justify-content:space-between;gap:1.5rem;margin-bottom:2rem;}
    .progress-title{font-size:1.9rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;letter-spacing:-.02em;}
    .progress-sub{color:var(--vg-text-muted);font-size:.88rem;}
    .progress-chip{color:var(--vg-text-muted);font-size:.78rem;font-weight:600;}
    .progress-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;margin-bottom:1.5rem;}
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
    .trend-bars{height:160px;display:flex;align-items:flex-end;gap:10px;padding-top:1rem;}
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
    .empty-progress{height:160px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;color:var(--vg-text-muted);font-size:.82rem;border:1px dashed var(--vg-border);border-radius:16px;background:var(--vg-sidebar);}
    .empty-prompt{margin-top:8px;color:var(--vg-accent);font-weight:700;font-size:.75rem;cursor:pointer;}
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
            <div class="metric-value">
                {{ $latest->weight ?? ($user->weight ?? '—') }}<span class="metric-unit">kg</span>
            </div>
            @if($user->target_weight)
                <p style="font-size:.65rem;color:var(--vg-text-faint);margin-top:4px;">Target: {{ $user->target_weight }}kg</p>
            @endif
            @if($weightChange !== null)
                <span class="metric-delta {{ $weightChange <= 0 ? 'delta-good' : 'delta-watch' }}">
                    {{ $weightChange > 0 ? '+' : '' }}{{ $weightChange }}kg since last entry
                </span>
            @elseif(!$latest && !$user->weight)
                <p style="font-size:.65rem;color:var(--vg-accent);margin-top:8px;font-weight:700;">Add first entry below</p>
            @endif
        </div>

        <div class="progress-card stagger-in delay-2">
            <p class="metric-label">Body Fat</p>
            <div class="metric-value">{{ $latest->body_fat_percentage ?? '—' }}<span class="metric-unit">%</span></div>
            @if($user->target_body_fat)
                <p style="font-size:.65rem;color:var(--vg-text-faint);margin-top:4px;">Target: {{ $user->target_body_fat }}%</p>
            @endif
            @if($bodyFatChange !== null)
                <span class="metric-delta {{ $bodyFatChange <= 0 ? 'delta-good' : 'delta-watch' }}">
                    {{ $bodyFatChange > 0 ? '+' : '' }}{{ $bodyFatChange }}% since last entry
                </span>
            @elseif(!$latest)
                <p style="font-size:.65rem;color:var(--vg-accent);margin-top:8px;font-weight:700;">Add first entry below</p>
            @endif
        </div>

        <div class="progress-card stagger-in delay-3">
            <p class="metric-label">Muscle Mass</p>
            <div class="metric-value">{{ $latest->muscle_mass ?? '—' }}<span class="metric-unit">%</span></div>
            @if($muscleChange !== null)
                <span class="metric-delta {{ $muscleChange >= 0 ? 'delta-good' : 'delta-watch' }}">
                    {{ $muscleChange > 0 ? '+' : '' }}{{ $muscleChange }}% since last entry
                </span>
            @elseif(!$latest)
                <p style="font-size:.65rem;color:var(--vg-accent);margin-top:8px;font-weight:700;">Add first entry below</p>
            @endif
        </div>

        <div class="progress-card stagger-in delay-4">
            <p class="metric-label">BMI</p>
            <div class="metric-value">{{ $latest->bmi ?? '—' }}<span class="metric-unit">kg/m²</span></div>
            @if($bmiChange !== null)
                <span class="metric-delta {{ ($bmiChange >= 18.5 && $bmiChange <= 25) ? 'delta-good' : 'delta-watch' }}">
                    {{ $bmiChange > 0 ? '+' : '' }}{{ $bmiChange }} since last entry
                </span>
            @endif
            <p style="font-size:.65rem;color:var(--vg-text-faint);margin-top:4px;">
                {{ $user->height ? 'Height: ' . $user->height . 'cm' : 'Set height below' }}
            </p>
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
                <div class="empty-progress">
                    <span>No entries yet to show trend.</span>
                    <label for="date" class="empty-prompt">Add your first entry below ↓</label>
                </div>
            @endif

            @if($metrics->count())
                <div style="margin-top:2rem;">
                    <h3 class="panel-title" style="margin-bottom:1rem;font-size:.9rem;">Body Measurements History</h3>
                    <div style="overflow-x:auto;background:var(--vg-sidebar);border-radius:16px;border:1px solid var(--vg-border);">
                        <table style="width:100%;border-collapse:collapse;font-size:.75rem;text-align:left;">
                            <thead>
                                <tr style="border-bottom:1px solid var(--vg-border);background:rgba(255,255,255,0.02);">
                                    <th style="padding:12px 16px;color:var(--vg-text-muted);">Date</th>
                                    <th style="padding:12px 16px;color:var(--vg-text-muted);">Weight</th>
                                    <th style="padding:12px 16px;color:var(--vg-text-muted);">Fat %</th>
                                    <th style="padding:12px 16px;color:var(--vg-text-muted);">Muscle %</th>
                                    <th style="padding:12px 16px;color:var(--vg-text-muted);">Waist</th>
                                    <th style="padding:12px 16px;color:var(--vg-text-muted);">Arms</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($metrics->take(8) as $entry)
                                    <tr style="border-bottom:1px solid var(--vg-border);">
                                        <td style="padding:12px 16px;color:var(--vg-text-strong);font-weight:700;">{{ optional($entry->date)->format('M d, Y') }}</td>
                                        <td style="padding:12px 16px;color:var(--vg-text-muted);">{{ $entry->weight ?? '—' }} kg</td>
                                        <td style="padding:12px 16px;color:var(--vg-text-muted);">{{ $entry->body_fat_percentage ?? '—' }}%</td>
                                        <td style="padding:12px 16px;color:var(--vg-text-muted);">{{ $entry->muscle_mass ?? '—' }}%</td>
                                        <td style="padding:12px 16px;color:var(--vg-text-muted);">{{ $entry->waist ?? '—' }} cm</td>
                                        <td style="padding:12px 16px;color:var(--vg-text-muted);">{{ $entry->arms ?? ($entry->biceps ?? '—') }} cm</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="margin-top:2rem;">
                    <h3 class="panel-title" style="margin-bottom:1rem;font-size:.9rem;">Weekly Progress Photos</h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(140px, 1fr));gap:1rem;">
                        @foreach($metrics->whereNotNull('progress_photo')->take(4) as $photoEntry)
                            <div style="aspect-ratio:3/4;border-radius:12px;overflow:hidden;background:var(--vg-sidebar);border:1px solid var(--vg-border);position:relative;">
                                <img src="{{ asset('storage/' . $photoEntry->progress_photo) }}" style="width:100%;height:100%;object-fit:cover;" alt="Progress Photo">
                                <div style="position:absolute;bottom:0;left:0;right:0;padding:8px;background:linear-gradient(transparent, rgba(0,0,0,0.8));">
                                    <p style="color:#fff;font-size:.65rem;font-weight:700;margin:0;">{{ optional($photoEntry->date)->format('M d') }}</p>
                                </div>
                            </div>
                        @endforeach
                        <div style="aspect-ratio:3/4;border-radius:12px;border:2px dashed var(--vg-border);display:flex;align-items:center;justify-content:center;color:var(--vg-text-faint);font-size:.7rem;text-align:center;padding:10px;">
                            Upload your weekly photo below
                        </div>
                    </div>
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
                        <label class="progress-label">Weight (kg)*</label>
                        <input class="progress-input" type="number" step="0.1" name="weight" value="{{ old('weight', $latest->weight ?? $user->weight) }}" required>
                        @error('weight')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="progress-label">Height (cm)</label>
                        <input class="progress-input" type="number" step="0.1" name="height" value="{{ old('height', $user->height ?? '') }}" placeholder="Needed for BMI">
                        @error('height')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label class="progress-label">Body Fat (%)</label>
                        <input class="progress-input" type="number" step="0.1" name="body_fat_percentage" value="{{ old('body_fat_percentage', $latest->body_fat_percentage ?? '') }}">
                        @error('body_fat_percentage')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="progress-label">Muscle Mass (%)</label>
                        <input class="progress-input" type="number" step="0.1" name="muscle_mass" value="{{ old('muscle_mass', $latest->muscle_mass ?? '') }}">
                        @error('muscle_mass')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label class="progress-label">Waist (cm)</label>
                        <input class="progress-input" type="number" step="0.1" name="waist" value="{{ old('waist', $latest->waist ?? '') }}">
                        @error('waist')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="progress-label">Arms (cm)</label>
                        <input class="progress-input" type="number" step="0.1" name="arms" value="{{ old('arms', $latest->arms ?? ($latest->biceps ?? '')) }}">
                        @error('arms')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns: 1fr 1fr;">
                    <div>
                        <label class="progress-label">Target Weight (kg)</label>
                        <input class="progress-input" type="number" step="0.1" name="target_weight" value="{{ old('target_weight', $user->target_weight ?? '') }}">
                        @error('target_weight')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="progress-label">Target Body Fat (%)</label>
                        <input class="progress-input" type="number" step="0.1" name="target_body_fat" value="{{ old('target_body_fat', $user->target_body_fat ?? '') }}">
                        @error('target_body_fat')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="progress-label">Progress Photo</label>
                    <input class="progress-input" type="file" name="progress_photo" accept="image/*" style="padding: 8px;">
                    <p style="font-size:.6rem;color:var(--vg-text-faint);margin-top:4px;">Upload a weekly photo to track visual progress.</p>
                    @error('progress_photo')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="progress-label">Notes</label>
                    <textarea class="progress-input" name="notes" rows="2" placeholder="Energy, soreness, measurements context...">{{ old('notes', $latest->notes ?? '') }}</textarea>
                    @error('notes')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                </div>

                <button class="save-btn" type="submit">Save Entry</button>
            </form>
        </div>
    </div>
</div>
@endsection
