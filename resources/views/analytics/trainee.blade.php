@extends('layouts.app')

@section('title', 'My Analytics')

@section('content')
<div style="max-width:1400px;margin:0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
        <div>
            <h1 style="font-size:1.8rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.3rem;letter-spacing:-.02em;">Fitness Analytics 📊</h1>
            <p style="color:var(--vg-text-muted);font-size:.85rem;">Track your progress and consistency</p>
        </div>
        <div style="text-align:right;">
            <p style="font-size:.72rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Live Analytics</p>
            <p id="analyticsLastUpdated" style="font-size:.78rem;color:var(--vg-text-faint);margin-top:3px;">Updated {{ now()->format('h:i A') }}</p>
        </div>
    </div>

    {{-- Top Stat Cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.2rem;margin-bottom:2rem;">
        <div class="fade-in-up delay-1" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.5rem;transition:all .35s;">
            <p style="color:var(--vg-text-muted);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;text-transform:uppercase;">Total Workouts</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">
                {{ $totalWorkouts > 0 ? $totalWorkouts : '—' }}
            </p>
        </div>
        
        <div class="fade-in-up delay-2" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.5rem;transition:all .35s;">
            <p style="color:var(--vg-text-muted);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;text-transform:uppercase;">Completion Rate</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#6ee7b7,#34d399);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">
                {{ $completionRate > 0 ? $completionRate . '%' : '—' }}
            </p>
        </div>
        
        <div class="fade-in-up delay-3" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.5rem;transition:all .35s;">
            <p style="color:var(--vg-text-muted);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;text-transform:uppercase;">Total Volume</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#fb923c,#f97316);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">
                {{ $totalVolume > 0 ? number_format($totalVolume) . ' kg' : '—' }}
            </p>
        </div>

        <div class="fade-in-up delay-4" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.5rem;transition:all .35s;">
            <p style="color:var(--vg-text-muted);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;text-transform:uppercase;">Total Reps</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#38bdf8,#0ea5e9);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">
                {{ $totalReps > 0 ? number_format($totalReps) : '—' }}
            </p>
        </div>
    </div>

    {{-- Workout Frequency & Volume Side-by-Side --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:1.5rem;margin-bottom:2rem;">
        
        {{-- Workout Frequency Bar Chart --}}
        <div class="fade-in-up delay-2" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.2rem;">📈 Workout Frequency</h2>
            <div style="display:flex;align-items:flex-end;gap:8px;height:140px;padding-top:20px;">
                @if(isset($workoutFrequency) && count($workoutFrequency) > 0)
                    @php $maxFreq = max(max($workoutFrequency), 1); @endphp
                    @foreach($workoutFrequency as $idx => $val)
                        @php
                            $height = ($val / $maxFreq) * 100;
                            $isCurrent = $idx === count($workoutFrequency) - 1;
                        @endphp
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;">
                            <div style="flex:1;width:100%;display:flex;align-items:flex-end;justify-content:center;">
                                <div style="width:70%;height:{{ $height }}%;background:{{ $isCurrent ? 'var(--vg-gradient)' : 'var(--vg-accent-soft)' }};border-radius:4px 4px 0 0;position:relative;transition:height .3s;">
                                    <span style="position:absolute;top:-20px;left:50%;transform:translateX(-50%);font-size:.7rem;color:{{ $isCurrent ? 'var(--vg-text-strong)' : 'var(--vg-text-muted)' }};font-weight:{{ $isCurrent ? '700' : 'normal' }};">{{ $val }}</span>
                                </div>
                            </div>
                            <span style="font-size:.65rem;color:var(--vg-text-muted);">W{{ $idx + 1 }}</span>
                        </div>
                    @endforeach
                @else
                    <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;padding-top:2rem;">No data available</div>
                @endif
            </div>
        </div>

        {{-- Volume Over Time Line Chart (Staggered dots for now) --}}
        <div class="fade-in-up delay-3" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.2rem;">🏋️ Volume Over Time</h2>
            <div style="display:flex;align-items:flex-end;gap:4px;height:140px;padding-top:20px;position:relative;">
                @if(isset($volumeOverTime) && count($volumeOverTime) > 0)
                    @php 
                        $maxVol = max(max($volumeOverTime), 1); 
                        $minVol = min($volumeOverTime);
                        $range = max($maxVol - $minVol, 1);
                    @endphp
                    {{-- SVG Line implementation --}}
                    <svg style="position:absolute;top:20px;left:0;width:100%;height:calc(100% - 20px);z-index:0;overflow:visible;" preserveAspectRatio="none">
                        @php
                            $points = [];
                            $step = 100 / (count($volumeOverTime) - 1);
                            foreach($volumeOverTime as $idx => $val) {
                                $x = $idx * $step;
                                $y = 100 - ((($val - $minVol) / $range) * 100);
                                $points[] = "$x%,$y%";
                            }
                            $path = implode(' ', $points);
                        @endphp
                        <polyline points="{{ str_replace('%', '', $path) }}" style="fill:none;stroke:var(--vg-accent);stroke-width:3;stroke-linejoin:round;stroke-linecap:round;" vector-effect="non-scaling-stroke"></polyline>
                    </svg>

                    @foreach($volumeOverTime as $idx => $val)
                        @php
                            $y = ((($val - $minVol) / $range) * 100);
                        @endphp
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;height:100%;position:relative;z-index:1;">
                            <div style="flex:1;width:100%;position:relative;">
                                <div style="position:absolute;bottom:{{ $y }}%;left:50%;transform:translate(-50%, 50%);width:12px;height:12px;background:var(--vg-panel-strong);border:3px solid var(--vg-accent);border-radius:50%;" title="{{ $val }} kg"></div>
                                @if($idx === count($volumeOverTime) - 1 || $idx === 0)
                                    <span style="position:absolute;bottom:calc({{ $y }}% + 15px);left:50%;transform:translateX(-50%);font-size:.65rem;color:var(--vg-text-strong);font-weight:700;">{{ round($val/1000, 1) }}k</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;padding-top:2rem;">No data available</div>
                @endif
            </div>
        </div>

    </div>

    {{-- Personal Records --}}
    <div class="fade-in-up delay-4" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.6rem;margin-bottom:2rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);">🏆 Personal Records</h2>
            <a href="{{ route('workouts.index') }}" style="font-size:.78rem;color:var(--vg-accent);font-weight:600;text-decoration:none;">View All →</a>
        </div>
        
        @if(isset($prs) && $prs->count() > 0)
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem;">
                @foreach($prs as $pr)
                    <div style="background:var(--vg-sidebar);border:1px solid var(--vg-border);border-radius:16px;padding:1.2rem;position:relative;overflow:hidden;">
                        <div style="position:absolute;top:0;right:0;width:60px;height:60px;background:var(--vg-accent-soft);border-radius:0 0 0 100%;z-index:0;"></div>
                        <div style="position:relative;z-index:1;">
                            <div style="font-size:1.8rem;margin-bottom:.5rem;">🏅</div>
                            <h3 style="font-size:.9rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:4px;">{{ $pr->exercise_name ?? 'Exercise' }}</h3>
                            <p style="font-size:1.4rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:8px;">
                                @if(isset($pr->weight)) {{ is_array($pr->weight) ? max($pr->weight) : $pr->weight }} kg @endif
                            </p>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:.75rem;color:var(--vg-text-muted);background:rgba(255,255,255,.05);padding:2px 8px;border-radius:6px;">{{ is_array($pr->reps) ? implode(', ', $pr->reps) : $pr->reps }} reps</span>
                                <span style="font-size:.65rem;color:var(--vg-text-faint);">{{ \Carbon\Carbon::parse($pr->created_at)->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center;padding:3rem 1rem;background:rgba(255,255,255,.015);border:1px dashed var(--vg-border-strong);border-radius:16px;">
                <div style="font-size:2.5rem;margin-bottom:1rem;opacity:.5;">🏅</div>
                <h3 style="color:var(--vg-text-strong);font-size:1.1rem;font-weight:700;margin-bottom:.5rem;">No personal records yet</h3>
                <p style="color:var(--vg-text-muted);font-size:.85rem;margin-bottom:1.5rem;">Keep training hard! Your best lifts will appear here.</p>
                <a href="{{ route('workouts.index') }}" style="display:inline-block;background:var(--vg-accent-soft);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:8px 20px;border-radius:10px;font-size:.8rem;font-weight:600;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--vg-accent-glow)'" onmouseout="this.style.background='var(--vg-accent-soft)'">Start a Workout</a>
            </div>
        @endif
    </div>

    {{-- Muscle Group Breakdown & Workout Duration --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:1.5rem;margin-bottom:2rem;">
        
        {{-- Muscle Group Pie/Donut Chart --}}
        <div class="fade-in-up delay-2" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.6rem;display:flex;flex-direction:column;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.5rem;">💪 Muscle Group Breakdown</h2>
            <div style="display:flex;gap:2rem;align-items:center;flex:1;">
                @if(isset($muscleBreakdown) && count($muscleBreakdown) > 0)
                    @php
                        $conicStops = [];
                        $currentPercentage = 0;
                        foreach($muscleBreakdown as $muscle) {
                            $nextPercentage = $currentPercentage + $muscle['value'];
                            $conicStops[] = $muscle['color'] . " $currentPercentage% $nextPercentage%";
                            $currentPercentage = $nextPercentage;
                        }
                        $conicGradient = implode(', ', $conicStops);
                    @endphp
                    <div style="width:140px;height:140px;border-radius:50%;background:conic-gradient({{ $conicGradient }});position:relative;flex-shrink:0;">
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:90px;height:90px;background:var(--vg-panel-strong);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <span style="font-size:1.2rem;">📊</span>
                        </div>
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;gap:.6rem;">
                        @foreach($muscleBreakdown as $muscle)
                            <div style="display:flex;align-items:center;justify-content:space-between;font-size:.8rem;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:10px;height:10px;border-radius:3px;background:{{ $muscle['color'] }};"></div>
                                    <span style="color:var(--vg-text-strong);">{{ $muscle['name'] }}</span>
                                </div>
                                <span style="color:var(--vg-text-muted);font-weight:600;">{{ $muscle['value'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;">No data available</div>
                @endif
            </div>
        </div>

        {{-- Workout Duration Trend --}}
        <div class="fade-in-up delay-3" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.2rem;">⏱️ Workout Duration Trend</h2>
            <div style="display:flex;align-items:flex-end;gap:8px;height:140px;padding-top:20px;">
                @if(isset($durationTrend) && count($durationTrend) > 0)
                    @php $maxDur = max(max($durationTrend), 1); @endphp
                    @foreach($durationTrend as $idx => $val)
                        @php
                            $height = ($val / $maxDur) * 100;
                            $isCurrent = $idx === count($durationTrend) - 1;
                        @endphp
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;height:100%;">
                            <div style="flex:1;width:100%;display:flex;align-items:flex-end;justify-content:center;">
                                <div style="width:40%;height:{{ $height }}%;background:{{ $isCurrent ? '#38bdf8' : 'rgba(56,189,248,.3)' }};border-radius:4px 4px 0 0;position:relative;transition:height .3s;">
                                    <span style="position:absolute;top:-20px;left:50%;transform:translateX(-50%);font-size:.65rem;color:{{ $isCurrent ? '#e0f2fe' : 'var(--vg-text-muted)' }};font-weight:{{ $isCurrent ? '700' : 'normal' }};">{{ $val }}m</span>
                                </div>
                            </div>
                            <span style="font-size:.65rem;color:var(--vg-text-muted);">S{{ $idx + 1 }}</span>
                        </div>
                    @endforeach
                @else
                    <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;padding-top:2rem;">No data available</div>
                @endif
            </div>
        </div>

    </div>

    {{-- Bottom Section: Heatmap, Comparison, Consistency --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;margin-bottom:2rem;">
        
        {{-- Best Performing Day Heatmap --}}
        <div class="fade-in-up delay-4" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.5rem;">📅 Best Performing Day</h2>
            @if(isset($bestDays) && count($bestDays) > 0)
                @php $maxDay = max(max($bestDays), 1); @endphp
                <div style="display:flex;gap:4px;height:80px;">
                    @foreach($bestDays as $day => $count)
                        @php 
                            $opacity = 0.1 + (($count / $maxDay) * 0.9);
                        @endphp
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;">
                            <div style="width:100%;flex:1;background:var(--vg-accent);opacity:{{ $opacity }};border-radius:6px;transition:opacity .3s;" title="{{ $count }} workouts"></div>
                            <span style="font-size:.65rem;color:var(--vg-text-muted);font-weight:600;">{{ substr($day, 0, 1) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;padding-top:1rem;">No data available</div>
            @endif
        </div>

        {{-- Progress Comparison --}}
        <div class="fade-in-up delay-5" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:20px;padding:1.6rem;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.5rem;">⚖️ This vs Last Month</h2>
            @if(isset($comparison))
                <div style="display:flex;flex-direction:column;gap:1.2rem;">
                    @foreach($comparison as $key => $data)
                        @php
                            $isPositive = strpos($data['trend'], '+') !== false;
                            $trendColor = $isPositive ? '#10b981' : '#f43f5e';
                            $icon = $key == 'workouts' ? '🏋️' : ($key == 'volume' ? '📦' : '🔥');
                        @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:10px;background:var(--vg-sidebar);display:flex;align-items:center;justify-content:center;font-size:1.2rem;">{{ $icon }}</div>
                                <div>
                                    <p style="font-size:.8rem;color:var(--vg-text-strong);text-transform:capitalize;font-weight:600;margin-bottom:2px;">{{ $key }}</p>
                                    <p style="font-size:.7rem;color:var(--vg-text-muted);">{{ is_numeric($data['current']) ? number_format($data['current']) : $data['current'] }} vs {{ is_numeric($data['previous']) ? number_format($data['previous']) : $data['previous'] }}</p>
                                </div>
                            </div>
                            <div style="font-size:.8rem;font-weight:700;color:{{ $trendColor }};background:{{ $isPositive ? 'rgba(16,185,129,.15)' : 'rgba(244,63,94,.15)' }};padding:4px 8px;border-radius:6px;">
                                {{ $data['trend'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;padding-top:1rem;">No data available</div>
            @endif
        </div>

        {{-- Consistency Score --}}
        <div class="fade-in-up delay-6" style="background:linear-gradient(135deg, var(--vg-accent-soft), rgba(255,255,255,.02));border:1px solid var(--vg-border-strong);border-radius:20px;padding:1.6rem;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.5rem;width:100%;text-align:left;">🎯 Consistency Score</h2>
            <div style="position:relative;width:120px;height:120px;margin-bottom:1rem;">
                <svg viewBox="0 0 36 36" style="width:100%;height:100%;transform:rotate(-90deg);">
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,.1)" stroke-width="3"/>
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--vg-accent-2)" stroke-width="3" stroke-dasharray="{{ $consistencyScore ?? 0 }}, 100" style="transition:stroke-dasharray 1s ease-out;"/>
                </svg>
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);text-align:center;">
                    <div style="font-size:1.8rem;font-weight:900;color:var(--vg-text-strong);line-height:1;">{{ $consistencyScore ?? 0 }}<span style="font-size:1rem;">%</span></div>
                </div>
            </div>
            <p style="font-size:.8rem;color:var(--vg-text-muted);line-height:1.4;">You've completed {{ $consistencyScore ?? 0 }}% of your planned workouts this month. Keep it up!</p>
        </div>

    </div>
</div>

<script>
    (function () {
        function signature(value) {
            return JSON.stringify(value);
        }

        const initialAnalytics = {!! json_encode([
            'totalWorkouts' => $totalWorkouts,
            'completedWorkouts' => $completedWorkouts,
            'completionRate' => $completionRate,
            'totalVolume' => $totalVolume,
            'totalReps' => $totalReps,
            'workoutFrequency' => $workoutFrequency,
            'volumeOverTime' => $volumeOverTime,
            'durationTrend' => $durationTrend,
            'muscleBreakdown' => $muscleBreakdown,
            'bestDays' => $bestDays,
            'comparison' => $comparison,
            'consistencyScore' => $consistencyScore,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};

        let lastSignature = signature(initialAnalytics);

        function refreshAnalytics() {
            fetch('{{ route('analytics.index') }}', {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store'
            })
                .then(response => response.ok ? response.json() : null)
                .then(payload => {
                    if (!payload) return;

                    const updated = document.getElementById('analyticsLastUpdated');
                    if (updated) {
                        updated.textContent = 'Updated ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    }

                    const nextSignature = signature({
                        totalWorkouts: payload.totalWorkouts,
                        completedWorkouts: payload.completedWorkouts,
                        completionRate: payload.completionRate,
                        totalVolume: payload.totalVolume,
                        totalReps: payload.totalReps,
                        workoutFrequency: payload.workoutFrequency,
                        volumeOverTime: payload.volumeOverTime,
                        durationTrend: payload.durationTrend,
                        muscleBreakdown: payload.muscleBreakdown,
                        bestDays: payload.bestDays,
                        comparison: payload.comparison,
                        consistencyScore: payload.consistencyScore
                    });

                    if (nextSignature !== lastSignature) {
                        window.location.reload();
                    }
                })
                .catch(() => {});
        }

        setInterval(refreshAnalytics, 15000);
    })();
</script>
@endsection
