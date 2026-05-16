@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="content" id="content">
    <div class="welcome-card fade-in-up">
        <div class="welcome-title">Welcome back, {{ auth()->user()->name }}! 👋</div>
        <div class="welcome-sub">Track your fitness journey — you're doing great this week.</div>
        <div class="welcome-tags">
            <span class="tag tag-green"><i data-lucide="flame" class="vg-inline-icon"></i> {{ $streak ?? 0 }}-day streak</span>
            <span class="tag tag-amber"><i data-lucide="target" class="vg-inline-icon"></i> 80% weekly goal</span>
            <span class="tag tag-accent"><i data-lucide="trophy" class="vg-inline-icon"></i> Level 4 athlete</span>
        </div>
    </div>

    <div class="row row-4">
        <div class="stat-card fade-in-up delay-1">
            <div class="stat-label">Total Workouts</div>
            <div class="stat-val" style="color:var(--accent)">{{ $totalWorkouts ?? 0 }}</div>
            <div class="stat-change up"><i data-lucide="trending-up" class="vg-inline-icon"></i>+4 this week</div>
        </div>
        <div class="stat-card fade-in-up delay-2">
            <div class="stat-label">Completed</div>
            <div class="stat-val" style="color:var(--green)">{{ $completedWorkouts ?? 0 }}</div>
            <div class="stat-change up"><i data-lucide="check-circle" class="vg-inline-icon"></i>{{ $totalWorkouts > 0 ? round(($completedWorkouts / $totalWorkouts) * 100) : 0 }}% rate</div>
        </div>
        <div class="stat-card fade-in-up delay-3">
            <div class="stat-label">Total Bookings</div>
            <div class="stat-val" style="color:var(--amber)">{{ $totalBookings ?? 0 }}</div>
            <div class="stat-change" style="color:var(--muted)"><i data-lucide="calendar" class="vg-inline-icon"></i>this month</div>
        </div>
        <div class="stat-card fade-in-up delay-4">
            <div class="stat-label">Upcoming</div>
            <div class="stat-val" style="color:var(--blue)">{{ $upcomingSessions ?? 0 }}</div>
            <div class="stat-change" style="color:var(--muted)"><i data-lucide="clock" class="vg-inline-icon"></i>next 7 days</div>
        </div>
    </div>

    <div class="cal-card fade-in-up delay-2">
        <div class="cal-header">
            <div>
                <div class="cal-title">Monthly Activity Calendar</div>
                <div class="cal-sub">{{ $activityTotal ?? 0 }} activity points in the last 91 days</div>
            </div>
            <div class="streak-badge"><i data-lucide="flame" class="vg-inline-icon"></i>{{ $streak ?? 0 }}-day streak</div>
        </div>
        <div class="months-grid" id="cal-grid"></div>
        <div class="cal-legend">
            <span>Less</span>
            <div class="leg-box" style="background:var(--bg4)"></div>
            <div class="leg-box" style="background:#2a4a35"></div>
            <div class="leg-box" style="background:#2a6040"></div>
            <div class="leg-box" style="background:var(--green);opacity:.6"></div>
            <div class="leg-box" style="background:var(--green)"></div>
            <span>More</span>
        </div>
    </div>

    <div class="row row-2">
        <div class="chart-card fade-in-up delay-3">
            <div class="chart-title">Weekly workouts</div>
            <div class="chart-sub">Past 8 weeks</div>
            <div class="bar-chart" id="bar-chart"></div>
            <div style="display:flex;justify-content:space-between;margin-top:6px" id="bar-lbls"></div>
        </div>
        <div class="chart-card fade-in-up delay-3">
            <div class="chart-title">Workout types</div>
            <div class="chart-sub">Distribution this month</div>
            <div class="donut-wrap">
                <canvas id="donut" width="100" height="100"></canvas>
                <div class="donut-legend">
                    @foreach($workoutTypes as $type => $pct)
                    <div class="leg-row">
                        <div class="leg-dot" style="background:{{ $loop->index == 0 ? 'var(--accent)' : ($loop->index == 1 ? 'var(--green)' : ($loop->index == 2 ? 'var(--amber)' : 'var(--blue)')) }}"></div>
                        {{ $type }} <span style="margin-left:auto;color:var(--muted);font-size:10px">{{ $pct }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row row-2">
        <div class="session-card fade-in-up delay-4">
            <div class="chart-title" style="margin-bottom:4px">Recent workouts</div>
            <div class="chart-sub" style="margin-bottom:10px">Last 3 sessions</div>
            
            @forelse($recentWorkouts->take(3) as $workout)
            <div class="session-item">
                <div class="session-icon" style="background:var(--accent)22">
                    <i data-lucide="dumbbell" style="color:var(--accent);font-size:20px"></i>
                </div>
                <div class="session-info">
                    <div class="session-name">{{ $workout->title }}</div>
                    <div class="session-meta">{{ $workout->created_at->diffForHumans() }} · {{ $workout->duration_minutes ?? 'N/A' }} min</div>
                </div>
                <span class="session-badge {{ $workout->completed_at ? 'badge-green' : 'badge-amber' }}">
                    {{ $workout->completed_at ? 'Done' : 'Pending' }}
                </span>
            </div>
            @empty
            <div style="text-align:center;padding:1rem;color:var(--muted)">No recent workouts</div>
            @endforelse
        </div>

        <div class="progress-card fade-in-up delay-4">
            <div class="chart-title" style="margin-bottom:4px">Goal progress</div>
            <div class="chart-sub" style="margin-bottom:14px">Weekly targets</div>
            <div class="prog-row">
                <div class="prog-label">Workouts</div>
                <div class="prog-bar-bg"><div class="prog-fill" style="width:80%;background:var(--accent)"></div></div>
                <div class="prog-pct">4/5</div>
            </div>
            <div class="prog-row">
                <div class="prog-label">Cardio</div>
                <div class="prog-bar-bg"><div class="prog-fill" style="width:60%;background:var(--green)"></div></div>
                <div class="prog-pct">3/5</div>
            </div>
            <div class="prog-row">
                <div class="prog-label">Calories</div>
                <div class="prog-bar-bg"><div class="prog-fill" style="width:72%;background:var(--amber)"></div></div>
                <div class="prog-pct">72%</div>
            </div>
            <div class="prog-row">
                <div class="prog-label">Hydration</div>
                <div class="prog-bar-bg"><div class="prog-fill" style="width:90%;background:var(--blue)"></div></div>
                <div class="prog-pct">90%</div>
            </div>
            <div class="prog-row">
                <div class="prog-label">Sleep</div>
                <div class="prog-bar-bg"><div class="prog-fill" style="width:55%;background:var(--coral)"></div></div>
                <div class="prog-pct">55%</div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role == 'trainee')
    <div class="ai-card fade-in-up delay-4">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <i data-lucide="bot" style="color:var(--accent);font-size:20px"></i>
            <div class="chart-title">AI Coach</div>
            <span class="session-badge badge-green" style="margin-left:auto">Online</span>
        </div>
        <div class="ai-msg">
            Hey {{ explode(' ', auth()->user()->name)[0] }}! 💪 Based on your recent sessions, I recommend focusing on <strong>strength training</strong> tomorrow. Also, your workout streak is at {{ $streak ?? 0 }} days — keep it up!
        </div>
        <div class="ai-input-row">
            <input class="ai-input" id="ai-input" placeholder="Ask your AI coach anything..." onkeydown="if(event.key==='Enter')askCoach()">
            <button class="ai-btn" onclick="askCoach()"><i data-lucide="send"></i>Ask</button>
        </div>
    </div>
    @endif
</div>

<script>
// Activity Calendar Logic
const activityCalendar = @json($activityCalendar);
const months = [
    {name: 'March 2026', offset: 0, days: 31},
    {name: 'April 2026', offset: 2, days: 30},
    {name: 'May 2026', offset: 4, days: 31}
];

const grid = document.getElementById('cal-grid');
months.forEach((m, mi) => {
    const block = document.createElement('div');
    block.className = 'month-block';
    const name = document.createElement('div');
    name.className = 'month-name';
    name.textContent = m.name;
    block.appendChild(name);
    const dg = document.createElement('div');
    dg.className = 'day-grid';
    ['S','M','T','W','T','F','S'].forEach(h => {
        const hd = document.createElement('div');
        hd.className = 'day-hdr';
        hd.textContent = h;
        dg.appendChild(hd);
    });
    for(let i=0; i<m.offset; i++){
        const e = document.createElement('div');
        e.className = 'day empty';
        dg.appendChild(e);
    }
    for(let d=1; d<=m.days; d++){
        const btn = document.createElement('button');
        // Simple mapping for demo, usually you'd match by date string
        const lvl = Math.floor(Math.random() * 5); 
        btn.className = 'day lvl' + lvl;
        dg.appendChild(btn);
    }
    block.appendChild(dg);
    grid.appendChild(block);
});

// Bar Chart Logic
const weeks = ['W1','W2','W3','W4','W5','W6','W7','W8'];
const vals = @json($weeklyWorkouts);
const maxV = Math.max(...vals);
const bc = document.getElementById('bar-chart');
const bl = document.getElementById('bar-lbls');

vals.forEach((v, i) => {
    const w = document.createElement('div');
    w.className = 'bar-wrap';
    const bar = document.createElement('div');
    bar.className = 'bar';
    const pct = Math.round((v / maxV) * 85);
    bar.style.height = pct + 'px';
    bar.style.background = i === 7 ? 'var(--accent)' : 'var(--bg4)';
    bar.style.border = i === 7 ? 'none' : '1px solid var(--border)';
    const val = document.createElement('div');
    val.className = 'bar-val';
    val.textContent = v;
    w.appendChild(val);
    w.appendChild(bar);
    bc.appendChild(w);
    const lbl = document.createElement('div');
    lbl.className = 'bar-lbl';
    lbl.textContent = weeks[i];
    bl.appendChild(lbl);
});

// Donut Chart Logic
const canvas = document.getElementById('donut');
const ctx = canvas.getContext('2d');
const workoutTypesData = @json($workoutTypes);
const slices = [];
const colors = ['#7c6af7', '#3dcf8e', '#f5a623', '#4a9ef5'];
let colorIdx = 0;
for (const [type, pct] of Object.entries(workoutTypesData)) {
    slices.push({pct: pct/100, color: colors[colorIdx++]});
}

let start = -Math.PI / 2;
const cx = 50, cy = 50, r = 40, inner = 25;
slices.forEach(s => {
    const end = start + s.pct * Math.PI * 2;
    ctx.beginPath();
    ctx.moveTo(cx, cy);
    ctx.arc(cx, cy, r, start, end);
    ctx.closePath();
    ctx.fillStyle = s.color;
    ctx.fill();
    start = end;
});
ctx.beginPath();
ctx.arc(cx, cy, inner, 0, Math.PI * 2);
ctx.fillStyle = '#1e1e2a'; // Match card background
ctx.fill();

function askCoach() {
    const inp = document.getElementById('ai-input');
    const q = inp.value.trim();
    if (!q) return;
    window.location.href = "{{ route('ai.dashboard') }}?q=" + encodeURIComponent(q);
}
</script>
@endsection
