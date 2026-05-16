@extends('layouts.app')

@section('title', 'My Workouts')

@section('content')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --glass-border-strong: rgba(255, 255, 255, 0.15);
        --accent-gradient: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
        --accent-glow: rgba(139, 92, 246, 0.4);
    }

    select {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 16px !important;
        padding-right: 40px !important;
        color: #ffffff !important;
        background-color: #0a0a1a !important;
    }
    select option {
        background-color: #0a0a1a !important;
        color: #ffffff !important;
        padding: 10px !important;
    }
    select * {
        color: #ffffff !important;
    }

    .workout-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .btn-premium {
        background: var(--accent-gradient);
        color: #fff;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 20px var(--accent-glow);
        text-decoration: none;
    }

    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px var(--accent-glow);
        filter: brightness(1.1);
    }

    .btn-outline {
        background: var(--glass-bg);
        color: #e2e8f0;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid var(--glass-border-strong);
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.2);
    }

    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 8px;
        background: var(--glass-bg);
        padding: 6px;
        border-radius: 14px;
        border: 1px solid var(--glass-border);
        margin-bottom: 2.5rem;
        width: fit-content;
    }

    .filter-tab {
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
    }

    .filter-tab.active {
        background: rgba(139, 92, 246, 0.15);
        color: #fff;
        box-shadow: inset 0 0 10px rgba(139, 92, 246, 0.1);
    }

    /* Active Plan Card */
    .active-plan-card {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);
        border: 1px solid rgba(139, 92, 246, 0.2);
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 3rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
    }

    .active-plan-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
        opacity: 0.2;
        pointer-events: none;
    }

    .progress-bar-container {
        width: 100%;
        height: 8px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        margin: 1rem 0;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--accent-gradient);
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    /* Workout Grid */
    .workout-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-bottom: 4rem;
    }

    .workout-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .workout-card:hover {
        transform: translateY(-5px);
        border-color: rgba(139, 92, 246, 0.3);
        background: rgba(255, 255, 255, 0.05);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .difficulty-badge {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 50px;
        letter-spacing: 0.05em;
    }

    .diff-beginner { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .diff-intermediate { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2); }
    .diff-advanced { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

    /* Templates */
    .template-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.2rem;
    }

    .template-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px dashed var(--glass-border-strong);
        border-radius: 18px;
        padding: 1.2rem;
        transition: all 0.2s;
    }

    .template-card:hover {
        background: rgba(255, 255, 255, 0.04);
        border-style: solid;
        border-color: var(--accent-glow);
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100vh;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(12px);
        display: none;
        justify-content: center;
        align-items: center; /* Back to center for safety */
        z-index: 9999;
        padding: 20px;
    }

    .modal-content {
        background: #0f172a;
        border: 1px solid var(--glass-border-strong);
        border-radius: 28px;
        width: 100%;
        max-width: 800px;
        max-height: calc(100vh - 100px); /* Strictly limited to fit screen */
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        margin-top: 0; /* Centered by flexbox */
    }

    .modal-body {
        overflow-y: auto;
        padding: 2rem;
        flex: 1; /* Grow to fill available space */
    }

    .modal-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0; /* Don't shrink header */
    }

    .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--glass-border);
        display: flex;
        gap: 12px;
        flex-shrink: 0; /* Don't shrink footer */
        background: rgba(15, 23, 42, 0.8); /* Slight transparency */
        backdrop-filter: blur(8px);
    }

    .modal-content form {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: rgba(255,255,255,0.6); margin-bottom: 8px; }
    .form-input {
        width: 100%;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 12px 16px;
        color: #fff;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    .form-input:focus { outline: none; border-color: #8b5cf6; background: rgba(255,255,255,0.08); }

    .exercise-item {
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--glass-border);
        border-radius: 14px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .fade-in { animation: fadeIn 0.4s ease forwards; }
</style>

<div class="max-w-7xl mx-auto px-4 py-6">
    
    {{-- Top Section --}}
    <div class="workout-header fade-in">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; background: var(--accent-gradient); -webkit-background-clip: text; background-clip: text; color: transparent;">
                My Workouts 💪
            </h1>
            <p style="color: rgba(255, 255, 255, 0.4); font-size: 0.95rem; margin-top: 4px;">
                @if(Auth::user()->role === 'trainer')
                    Track, create and excel in your fitness journey
                @else
                    Track and excel in your fitness journey
                @endif
            </p>
        </div>
        
        <div style="display: flex; gap: 12px;">
            @if(Auth::user()->role === 'trainer')
                <button onclick="openModal()" class="btn-premium">
                    <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Create Workout
                </button>
                <a href="#templates-section" class="btn-outline">
                    <i data-lucide="layout-grid" style="width: 18px; height: 18px;"></i> Browse Templates
                </a>
            @endif
        </div>
    </div>

    {{-- Filter Tabs (Trainee Only) --}}
    @if(Auth::user()->role === 'trainee')
    <div class="filter-tabs fade-in" style="animation-delay: 0.1s;">
        <button class="filter-tab active" onclick="filterWorkouts('all')">All</button>
        <button class="filter-tab" onclick="filterWorkouts('strength')">Strength</button>
        <button class="filter-tab" onclick="filterWorkouts('cardio')">Cardio</button>
        <button class="filter-tab" onclick="filterWorkouts('hiit')">HIIT</button>
        <button class="filter-tab" onclick="filterWorkouts('flexibility')">Flexibility</button>
    </div>
    @endif

    {{-- 1. Active Workout Plan Card (Trainee Only) --}}
    @if(Auth::user()->role === 'trainee' && ($nextWorkout || true)) {{-- Keeping it visible for now with dummy data as requested --}}
    <div class="active-plan-card fade-in" style="animation-delay: 0.2s;">
        <div style="flex: 1; max-width: 60%;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <span style="background: rgba(139, 92, 246, 0.2); color: #a78bfa; font-size: 0.65rem; font-weight: 800; padding: 4px 10px; border-radius: 50px; text-transform: uppercase;">Active Plan</span>
                <span style="color: rgba(255, 255, 255, 0.4); font-size: 0.8rem;">• Last active 2 days ago</span>
            </div>
            <h2 style="font-size: 1.8rem; font-weight: 800; color: #fff; margin-bottom: 4px;">{{ $nextWorkout->title ?? 'Hypertrophy: 5 Day Split 🔥' }}</h2>
            <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 1rem;">
                <p style="color: rgba(255, 255, 255, 0.5); font-size: 0.9rem; margin: 0;">Week 2 of 4 • Next session: Upper Body Power</p>
                <div style="display: flex; align-items: center; gap: 5px; color: #10b981; font-size: 0.8rem; font-weight: 700;">
                    <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                    <span>Est. {{ $nextWorkout->duration_minutes ?? '45' }} min</span>
                </div>
            </div>
            
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: 35%;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: rgba(255, 255, 255, 0.4);">
                <span>Progress: 35%</span>
                <span>7 of 20 sessions</span>
            </div>
        </div>
        
        <div style="text-align: right;">
            @if($nextWorkout)
                <a href="{{ route('workouts.show', $nextWorkout->id) }}" class="btn-premium" style="padding: 14px 28px; font-size: 1rem;">
                    Start Workout <i data-lucide="play" style="width: 20px; height: 20px;"></i>
                </a>
            @else
                <button onclick="openModal()" class="btn-premium" style="padding: 14px 28px; font-size: 1rem;">
                    Browse Workouts <i data-lucide="search" style="width: 20px; height: 20px;"></i>
                </button>
            @endif
        </div>
    </div>
    @endif

    {{-- 2. Assigned By Trainer Section (Trainee Only) --}}
    @if(Auth::user()->role === 'trainee')
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;" class="fade-in">
        <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff;">Assigned By Trainer</h3>
        <span style="color: rgba(255, 255, 255, 0.3); font-size: 0.85rem;">{{ count($assignedWorkouts) }} workouts</span>
    </div>

    @if(count($assignedWorkouts) > 0)
        <div class="workout-grid fade-in" style="animation-delay: 0.3s;">
            @foreach($assignedWorkouts as $workout)
                @include('workouts._card', ['workout' => $workout, 'showActions' => false])
            @endforeach
        </div>
    @else
        <div style="background: var(--glass-bg); border: 1px dashed var(--glass-border-strong); border-radius: 24px; text-align: center; padding: 4rem 2rem; margin-bottom: 4rem;" class="fade-in">
            <div style="font-size: 3.5rem; opacity: 0.2; margin-bottom: 1rem;">📋</div>
            <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">No assigned workouts</h3>
            <p style="color: rgba(255, 255, 255, 0.4); font-size: 0.95rem; margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto;">
                Ask your trainer to assign a workout to get started on your guided journey!
            </p>
            <a href="{{ route('chat.index') }}" class="btn-outline" style="border-color: var(--vg-accent); color: var(--vg-accent);">
                <i data-lucide="message-square" style="width: 18px; height: 18px;"></i> Message Trainer
            </a>
        </div>
    @endif

    {{-- 3. My Custom Workouts Section (Trainee Only) --}}
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; padding-top: 2rem;" class="fade-in">
        <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff;">My Custom Workouts</h3>
        <div style="display: flex; gap: 12px; align-items: center;">
            <span style="color: rgba(255, 255, 255, 0.3); font-size: 0.85rem; margin-right: 12px;">{{ count($customWorkouts) }} workouts</span>
            <button onclick="openModal()" class="btn-premium" style="padding: 8px 16px; font-size: 0.8rem;">
                <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Create Own
            </button>
        </div>
    </div>

    @if(count($customWorkouts) > 0)
        <div class="workout-grid fade-in" style="animation-delay: 0.3s;">
            @foreach($customWorkouts as $workout)
                @include('workouts._card', ['workout' => $workout, 'showActions' => true])
            @endforeach
        </div>
    @else
        <div style="background: var(--glass-bg); border: 1px dashed var(--glass-border-strong); border-radius: 24px; text-align: center; padding: 3rem 2rem; margin-bottom: 4rem;" class="fade-in">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: rgba(255,255,255,0.6); margin-bottom: 0.5rem;">Create your own routines</h3>
            <p style="color: rgba(255, 255, 255, 0.3); font-size: 0.85rem; margin-bottom: 1.5rem;">Want to do something extra? Build your own custom workouts.</p>
            <button onclick="openModal()" class="btn-outline">
                Start Building
            </button>
        </div>
    @endif
    @endif

    {{-- 4. Client Workouts Section (Trainer Only) --}}
    @if(Auth::user()->role === 'trainer')
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;" class="fade-in">
        <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff;">Client Workouts</h3>
        <span style="color: rgba(255, 255, 255, 0.3); font-size: 0.85rem;">Showing {{ $workouts->total() }} total</span>
    </div>

    @if($workouts->count() > 0)
        <div class="workout-grid fade-in" style="animation-delay: 0.3s;">
            @foreach($workouts as $workout)
                @include('workouts._card', ['workout' => $workout, 'showActions' => true, 'showClient' => true])
            @endforeach
        </div>
        <div class="mt-8">
            {{ $workouts->links() }}
        </div>
    @else
        <div style="background: var(--glass-bg); border: 1px dashed var(--glass-border-strong); border-radius: 24px; text-align: center; padding: 4rem 2rem; margin-bottom: 4rem;" class="fade-in">
            <div style="font-size: 3.5rem; opacity: 0.2; margin-bottom: 1rem;">🏋️</div>
            <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">No workouts found</h3>
            <p style="color: rgba(255, 255, 255, 0.4); font-size: 0.95rem; margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto;">Start building your perfect routine for your clients!</p>
            <button onclick="openModal()" class="btn-premium">
                Create First Client Workout
            </button>
        </div>
    @endif
    @endif

    {{-- 3. Workout Templates Section --}}
    @if(Auth::user()->role === 'trainer')
    <div id="templates-section" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; padding-top: 2rem;" class="fade-in">
        <div>
            <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff;">Workout Templates</h3>
            <p style="color: rgba(255, 255, 255, 0.3); font-size: 0.85rem;">Professional pre-built routines</p>
        </div>
    </div>

    <div class="template-grid fade-in" style="animation-delay: 0.4s;">
        @php
            $templates = [
                ['name' => 'Push Pull Legs (PPL)', 'desc' => 'Classic 3-day split for mass', 'diff' => 'Intermediate', 'dur' => '65m', 'ex' => 8, 'icon' => 'layers'],
                ['name' => 'Full Body Ignition', 'desc' => 'High intensity total body blast', 'diff' => 'Beginner', 'dur' => '45m', 'ex' => 10, 'icon' => 'zap'],
                ['name' => 'HIIT Cardio Burn', 'desc' => 'Max calorie burn in 20 mins', 'diff' => 'Advanced', 'dur' => '20m', 'ex' => 6, 'icon' => 'flame'],
                ['name' => 'Lower Body Focus', 'desc' => 'Strong legs and glutes', 'diff' => 'Intermediate', 'dur' => '55m', 'ex' => 7, 'icon' => 'chevron-down']
            ];
        @endphp

        @foreach($templates as $tmp)
            <div class="template-card">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2); display: flex; align-items: center; justify-content: center; margin-bottom: 1.2rem; color: #a78bfa;">
                    <i data-lucide="{{ $tmp['icon'] }}" style="width: 20px; height: 20px;"></i>
                </div>
                <h4 style="font-size: 1.05rem; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $tmp['name'] }}</h4>
                <p style="color: rgba(255, 255, 255, 0.4); font-size: 0.75rem; margin-bottom: 1.2rem;">{{ $tmp['desc'] }}</p>
                
                <div style="display: flex; gap: 12px; margin-bottom: 1.5rem;">
                    <div style="font-size: 0.7rem; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.05em;">
                        <span style="color: #fff; font-weight: 700;">{{ $tmp['diff'] }}</span>
                    </div>
                    <div style="font-size: 0.7rem; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.05em;">
                        <span style="color: #fff; font-weight: 700;">{{ $tmp['dur'] }}</span>
                    </div>
                </div>
                
                <form action="{{ route('workouts.use-template') }}" method="POST">
                    @csrf
                    <input type="hidden" name="template_name" value="{{ $tmp['name'] }}">
                    <button type="submit" class="btn-premium" style="width: 100%; font-size: 0.75rem; padding: 8px; justify-content: center;">
                        <i data-lucide="user-plus" style="width: 14px; height: 14px;"></i> Assign to Client
                    </button>
                </form>
            </div>
        @endforeach
    </div>
    @endif
@endsection

@push('modals')
{{-- Create Workout Modal --}}
<div id="workoutModal" class="modal-overlay">
    <div class="modal-content">
        <form action="{{ route('workouts.store') }}" method="POST">
            @csrf
            
            <div class="modal-header">
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #fff;">
                        {{ Auth::user()->role === 'trainer' ? 'Create Client Workout' : 'Create Custom Workout' }} ✨
                    </h2>
                    <p style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">
                        {{ Auth::user()->role === 'trainer' ? 'Build a routine for your client' : 'Build your custom training routine' }}
                    </p>
                </div>
                <button type="button" onclick="closeModal()" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: #fff; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>

            <div class="modal-body">
                @if(Auth::user()->role === 'trainer')
                    <div class="form-group" style="margin-bottom: 2rem; background: rgba(139, 92, 246, 0.05); padding: 1.5rem; border-radius: 20px; border: 1px solid rgba(139, 92, 246, 0.2);">
                        <label class="form-label" style="color: #a78bfa;">1. Select Client to Assign</label>
                        <select name="trainee_id" class="form-input" style="border-color: rgba(139, 92, 246, 0.4); font-size: 1.1rem; padding: 15px;" required>
                            <option value="">-- Choose a Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">Workout Title</label>
                        <input type="text" name="title" class="form-input" placeholder="e.g. Morning Push Day" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Difficulty</label>
                        <select name="difficulty" class="form-input">
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-input">
                            <option value="Strength">Strength</option>
                            <option value="Cardio">Cardio</option>
                            <option value="HIIT">HIIT</option>
                            <option value="Flexibility">Flexibility</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Duration (mins)</label>
                        <input type="number" name="duration_minutes" class="form-input" placeholder="45">
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3 style="font-size: 1rem; font-weight: 700; color: #fff;">Exercises</h3>
                        <button type="button" onclick="addExerciseRow()" style="color: #a78bfa; background: none; border: none; font-size: 0.85rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Add Exercise
                        </button>
                    </div>

                    <div id="exerciseList">
                        {{-- Initial Exercise Row --}}
                        <div class="exercise-item">
                            <div style="flex: 1;">
                                <label class="form-label">Exercise</label>
                                <select name="exercises[0][exercise_id]" class="form-input" required>
                                    <option value="">Search exercise...</option>
                                    @foreach($exercises as $exercise)
                                        <option value="{{ $exercise->id }}">{{ $exercise->name }} ({{ $exercise->muscle_group }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="width: 80px;">
                                <label class="form-label">Sets</label>
                                <input type="number" name="exercises[0][sets]" class="form-input" value="3" required>
                            </div>
                            <div style="width: 80px;">
                                <label class="form-label">Reps</label>
                                <input type="number" name="exercises[0][reps]" class="form-input" value="10" required>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" style="margin-top: 24px; background: none; border: none; color: rgba(255,255,255,0.2); cursor: pointer;">
                                <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-premium" style="flex: 1; justify-content: center;">Save & Create Workout</button>
                <button type="button" onclick="closeModal()" class="btn-outline" style="flex: 0.3; justify-content: center;">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endpush

@push('scripts')
<script>
    function openModal() {
        document.getElementById('workoutModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('workoutModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    let exerciseCount = 1;
    function addExerciseRow() {
        const list = document.getElementById('exerciseList');
        const row = document.createElement('div');
        row.className = 'exercise-item fade-in';
        row.innerHTML = `
            <div style="flex: 1;">
                <label class="form-label">Exercise</label>
                <select name="exercises[${exerciseCount}][exercise_id]" class="form-input" required>
                    <option value="">Search exercise...</option>
                    @foreach($exercises as $exercise)
                        <option value="{{ $exercise->id }}">{{ $exercise->name }} ({{ $exercise->muscle_group }})</option>
                    @endforeach
                </select>
            </div>
            <div style="width: 80px;">
                <label class="form-label">Sets</label>
                <input type="number" name="exercises[${exerciseCount}][sets]" class="form-input" value="3" required>
            </div>
            <div style="width: 80px;">
                <label class="form-label">Reps</label>
                <input type="number" name="exercises[${exerciseCount}][reps]" class="form-input" value="10" required>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="margin-top: 24px; background: none; border: none; color: rgba(255,255,255,0.2); cursor: pointer;">
                <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
            </button>
        `;
        list.appendChild(row);
        exerciseCount++;
        lucide.createIcons();
    }

    function filterWorkouts(type) {
        // Update tabs
        const tabs = document.querySelectorAll('.filter-tab');
        tabs.forEach(tab => tab.classList.remove('active'));
        if (event && event.target) {
            event.target.classList.add('active');
        }

        // Filter cards
        const cards = document.querySelectorAll('.workout-card');
        cards.forEach(card => {
            if (type === 'all' || card.getAttribute('data-type') === type) {
                card.style.display = 'flex';
                card.classList.add('fade-in');
            } else {
                card.style.display = 'none';
            }
        });
    }

    function deleteWorkout(id) {
        if (confirm('Are you sure you want to delete this workout?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    // Make cards clickable
    document.querySelectorAll('.workout-card').forEach(card => {
        card.addEventListener('click', (e) => {
            // Don't trigger if clicking buttons, links, or their children
            if (e.target.closest('button') || e.target.closest('a') || e.target.closest('form')) return;
            
            const workoutId = card.getAttribute('data-id');
            if (workoutId) {
                window.location.href = `/workouts/${workoutId}`;
            }
        });
    });

    // Initialize Lucide icons for dynamic elements
    window.addEventListener('load', () => {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@endpush