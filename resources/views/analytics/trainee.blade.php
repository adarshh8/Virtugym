@extends('layouts.app')

@section('title', 'My Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Fitness Analytics 📊</h1>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Total Workouts</p>
            <p class="text-3xl font-bold text-purple-600">{{ $totalWorkouts ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Completion Rate</p>
            <p class="text-3xl font-bold text-green-600">{{ $completionRate ?? 0 }}%</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Total Volume</p>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($totalVolume ?? 0) }} kg</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <p class="text-gray-500 text-sm">Total Reps</p>
            <p class="text-3xl font-bold text-orange-600">{{ number_format($totalReps ?? 0) }}</p>
        </div>
        <div style="text-align:right;">
            <p style="font-size:.72rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Live Analytics</p>
            <p id="analyticsLastUpdated" style="font-size:.78rem;color:var(--vg-text-faint);margin-top:3px;">Updated {{ now()->format('h:i A') }}</p>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-bold mb-4">Recent Personal Records 🏆</h2>
        @if(isset($prs) && $prs->count() > 0)
<<<<<<< Updated upstream
            @foreach($prs as $pr)
                <div class="border-b pb-3 mb-3">
                    <p class="font-semibold">{{ $pr->exercise_name ?? 'Exercise' }}</p>
                    <p class="text-sm text-gray-500">
                        @if(isset($pr->weight)) {{ $pr->weight }} kg × @endif
                        @if(isset($pr->reps)) {{ is_array($pr->reps) ? implode(', ', $pr->reps) : $pr->reps }} reps @endif
                    </p>
                </div>
            @endforeach
=======
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
>>>>>>> Stashed changes
        @else
            <p class="text-gray-500">No personal records yet. Keep training!</p>
        @endif
    </div>
    
    @if(isset($weeklyProgress) && count($weeklyProgress) > 0)
    <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
        <h2 class="text-lg font-bold mb-4">Monthly Progress</h2>
        <div class="space-y-3">
            @foreach($weeklyProgress as $week => $count)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $week }}</span>
                        <span>{{ $count }} workouts</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 rounded-full h-2" style="width: {{ min(100, $count * 10) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
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
