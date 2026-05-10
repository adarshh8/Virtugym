@props([
    'calendar' => collect(),
    'total' => 0,
    'streak' => 0,
])

@php
    $calendar = collect($calendar);
    $months = $calendar->groupBy(fn ($day) => \Carbon\Carbon::parse($day['date'])->format('Y-m'));

    $cellColor = function (int $level): string {
        return match ($level) {
            4 => '#22c55e',
            3 => '#16a34a',
            2 => '#4ade80',
            1 => '#14532d',
            default => 'rgba(255,255,255,.055)',
        };
    };
@endphp

<section class="fade-in-up" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:20px;padding:1.4rem;margin-bottom:2rem;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;">
        <div>
            <h2 style="font-size:1rem;font-weight:700;color:#e2d9f3;margin-bottom:.25rem;">Monthly Activity Calendar</h2>
            @if($total > 0)
                <p style="font-size:.78rem;color:rgba(255,255,255,.35);">{{ $total }} activity {{ $total === 1 ? 'point' : 'points' }} in the last {{ $calendar->count() }} days</p>
            @endif
        </div>
        <div style="text-align:right;">
            <div style="font-size:1.8rem;font-weight:900;background:linear-gradient(135deg,#6ee7b7,#34d399);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;">{{ $streak }}</div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.35);margin-top:2px;">24h streak</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
        @foreach($months as $monthKey => $days)
            @php
                $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth();
                $visibleDates = $days->keyBy('date');
                $daysInMonth = $monthDate->daysInMonth;
                $firstWeekday = $monthDate->dayOfWeek;
            @endphp
            <div style="background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.07);border-radius:14px;padding:.9rem;">
                <div style="font-size:.82rem;font-weight:700;color:#e2d9f3;margin-bottom:.75rem;">{{ $monthDate->format('F Y') }}</div>
                <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:5px;color:rgba(255,255,255,.32);font-size:.62rem;text-align:center;">
                    @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $weekday)
                        <span>{{ $weekday }}</span>
                    @endforeach
                </div>
                <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;">
                    @for($blank = 0; $blank < $firstWeekday; $blank++)
                        <span style="aspect-ratio:1/1;"></span>
                    @endfor

                    @for($dayNumber = 1; $dayNumber <= $daysInMonth; $dayNumber++)
                        @php
                            $date = $monthDate->copy()->day($dayNumber)->toDateString();
                            $day = $visibleDates->get($date, [
                                'date' => $date,
                                'label' => $monthDate->copy()->day($dayNumber)->format('M d, Y'),
                                'count' => 0,
                                'level' => 0,
                            ]);
                        @endphp
                        <span
                            title="{{ $day['label'] }}: {{ $day['count'] }} activity {{ $day['count'] === 1 ? 'point' : 'points' }}"
                            aria-label="{{ $day['label'] }}: {{ $day['count'] }} activity {{ $day['count'] === 1 ? 'point' : 'points' }}"
                            style="aspect-ratio:1/1;border-radius:4px;border:1px solid rgba(255,255,255,.06);background:{{ $cellColor((int) $day['level']) }};box-shadow:{{ $day['level'] > 0 ? '0 0 0 1px rgba(34,197,94,.12)' : 'none' }};">
                        </span>
                    @endfor
                </div>
            </div>
        @endforeach
    </div>

    <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;margin-top:.9rem;color:rgba(255,255,255,.35);font-size:.72rem;">
        <span>Less</span>
        @foreach([0, 1, 2, 3, 4] as $level)
            <span style="width:12px;height:12px;border-radius:3px;border:1px solid rgba(255,255,255,.06);background:{{ $cellColor($level) }};"></span>
        @endforeach
        <span>More</span>
    </div>
</section>
