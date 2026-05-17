@extends('layouts.app')

@section('title', 'Exercise Library')

@section('content')
<style>
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
    }
    select option {
        background-color: #0a0a1a !important;
        color: #ffffff !important;
        padding: 10px !important;
    }
    /* Reinforce white text for all children of select */
    select * {
        color: #ffffff !important;
    }
    /* Specific for Chrome/Safari to style the dropdown menu */
    select:-webkit-autofill,
    select:-webkit-autofill:hover,
    select:-webkit-autofill:focus {
        -webkit-text-fill-color: #ffffff !important;
        -webkit-box-shadow: 0 0 0px 1000px #0a0a1a inset !important;
    }
</style>
<div style="max-width:1280px;margin:0 auto;">
    <!-- Header -->
    <div style="margin-bottom:2.5rem; display: flex; justify-content: space-between; align-items: flex-end;" class="fade-in-up">
        <div>
            <h1 style="font-size:1.8rem;font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
                Exercise Library 📚
            </h1>
            <p style="color:rgba(255,255,255,.4);font-size:.9rem;">
                Explore <span style="color: var(--vg-accent); font-weight: 700;">{{ $exercises->total() }} exercises</span> across multiple disciplines
            </p>
        </div>
        
        <div style="display: flex; gap: 12px; align-items: center;">
            <button onclick="filterSaved(this)" class="btn-outline" id="savedFilterBtn" style="border-radius: 14px; padding: 12px 20px; display: inline-flex; align-items: center; gap: 8px; height: 46px;">
                <i data-lucide="bookmark" style="width: 18px; height: 18px;"></i>
                <span style="font-weight: 600;">Saved</span>
            </button>
            @if(Auth::user()->role === 'trainer')
                <button onclick="openCustomExerciseModal()" class="btn-premium" style="height: 46px; display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;">
                    <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                    <span style="font-weight: 600;">Add Custom Exercise</span>
                </button>
            @endif
        </div>
    </div>
    
    <!-- Search & Filters -->
    <div style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.12);border-radius:24px;padding:1.8rem;margin-bottom:2.5rem; backdrop-filter: blur(10px);" class="fade-in-up delay-1">
        <form id="filterForm" method="GET" action="{{ route('exercises.index') }}">
            <div style="display:grid;grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1.5rem; align-items: flex-end;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:800;color:rgba(196,181,253,.5);letter-spacing:.08em;margin-bottom:10px;text-transform:uppercase;">Search Library</label>
                    <div style="position: relative;">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               style="width:100%;padding:14px 14px 14px 44px;background:rgba(8,8,26,0.6);border:1px solid rgba(139,92,246,.2);border-radius:14px;color:#fff;font-size:.9rem;outline:none;transition: all 0.3s;"
                               placeholder="e.g. Bench Press, Deadlift..."
                               onfocus="this.style.borderColor='rgba(139,92,246,0.5)';this.style.background='rgba(8,8,26,0.8)'" onblur="this.style.borderColor='rgba(139,92,246,.2)';this.style.background='rgba(8,8,26,0.6)'">
                        <i data-lucide="search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: rgba(255,255,255,0.25);"></i>
                        
                        @if(request('search') || request('muscle_group') || request('equipment') || request('difficulty'))
                            <a href="{{ route('exercises.index') }}" 
                               style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: rgba(239, 68, 68, 0.6); font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none;"
                               onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='rgba(239, 68, 68, 0.6)'">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:800;color:rgba(196,181,253,.5);letter-spacing:.08em;margin-bottom:10px;text-transform:uppercase;">Muscle Group</label>
                    <select name="muscle_group" onchange="this.form.submit()"
                            style="width:100%;padding:14px 14px;background:#0a0a1a;border:1px solid rgba(139,92,246,.2);border-radius:14px;color:#fff;font-size:.9rem;outline:none;cursor: pointer;">
                        <option value="" style="background: #0a0a1a !important; color: #fff !important;">All Groups</option>
                        @foreach($muscleGroups as $group)
                            <option value="{{ $group }}" {{ request('muscle_group') == $group ? 'selected' : '' }} style="background: #0a0a1a !important; color: #fff !important;">
                                {{ $group }} ({{ $muscleCounts[$group] ?? 0 }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:800;color:rgba(196,181,253,.5);letter-spacing:.08em;margin-bottom:10px;text-transform:uppercase;">Equipment</label>
                    <select name="equipment" onchange="this.form.submit()"
                            style="width:100%;padding:14px 14px;background:#0a0a1a;border:1px solid rgba(139,92,246,.2);border-radius:14px;color:#fff;font-size:.9rem;outline:none;cursor: pointer;">
                        <option value="" style="background: #0a0a1a !important; color: #fff !important;">All Equipment</option>
                        @foreach($equipmentList as $equip)
                            <option value="{{ $equip }}" {{ request('equipment') == $equip ? 'selected' : '' }} style="background: #0a0a1a !important; color: #fff !important;">
                                {{ $equip }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:800;color:rgba(196,181,253,.5);letter-spacing:.08em;margin-bottom:10px;text-transform:uppercase;">Level</label>
                    <select name="difficulty" onchange="this.form.submit()"
                            style="width:100%;padding:14px 14px;background:#0a0a1a;border:1px solid rgba(139,92,246,.2);border-radius:14px;color:#fff;font-size:.9rem;outline:none;cursor: pointer;">
                        <option value="" style="background: #0a0a1a !important; color: #fff !important;">All Levels</option>
                        @foreach($difficulties as $diff)
                            <option value="{{ $diff }}" {{ request('difficulty') == $diff ? 'selected' : '' }} style="background: #0a0a1a !important; color: #fff !important;">
                                {{ $diff }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Exercises Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.8rem;">
        @forelse($exercises as $exercise)
            @php
                $muscleColors = [
                    'Chest' => ['bg' => 'rgba(239,68,68,.1)', 'text' => '#f87171', 'border' => 'rgba(239,68,68,.2)', 'icon' => 'layers', 'grad' => 'linear-gradient(135deg, #450a0a 0%, #000 100%)'],
                    'Back' => ['bg' => 'rgba(59,130,246,.1)', 'text' => '#60a5fa', 'border' => 'rgba(59,130,246,.2)', 'icon' => 'anchor', 'grad' => 'linear-gradient(135deg, #172554 0%, #000 100%)'],
                    'Legs' => ['bg' => 'rgba(16,185,129,.1)', 'text' => '#34d399', 'border' => 'rgba(16,185,129,.2)', 'icon' => 'box', 'grad' => 'linear-gradient(135deg, #064e3b 0%, #000 100%)'],
                    'Shoulders' => ['bg' => 'rgba(245,158,11,.1)', 'text' => '#fbbf24', 'border' => 'rgba(245,158,11,.2)', 'icon' => 'target', 'grad' => 'linear-gradient(135deg, #451a03 0%, #000 100%)'],
                    'Arms' => ['bg' => 'rgba(139,92,246,.1)', 'text' => '#a78bfa', 'border' => 'rgba(139,92,246,.2)', 'icon' => 'dumbbell', 'grad' => 'linear-gradient(135deg, #2e1065 0%, #000 100%)'],
                    'Core' => ['bg' => 'rgba(236,72,153,.1)', 'text' => '#f472b6', 'border' => 'rgba(236,72,153,.2)', 'icon' => 'activity', 'grad' => 'linear-gradient(135deg, #500724 0%, #000 100%)'],
                ];
                $style = $muscleColors[$exercise->muscle_group] ?? ['bg' => 'rgba(255,255,255,.05)', 'text' => '#ccc', 'border' => 'rgba(255,255,255,.1)', 'icon' => 'circle', 'grad' => 'linear-gradient(135deg, #1f2937 0%, #000 100%)'];
                
                $calories = match($exercise->difficulty) {
                    'Beginner' => 35,
                    'Intermediate' => 55,
                    'Advanced' => 75,
                    default => 45
                };
            @endphp
            
            <div class="exercise-card fade-in" data-exercise-id="{{ (string)$exercise->id }}" style="background:rgba(255,255,255,.02);border:1px solid rgba(139,92,246,.12);border-radius:28px;overflow:hidden;transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1);display:flex;flex-direction:column;position:relative;height: 100%;">
                
                {{-- Prominent Illustration Section --}}
                <div style="height: 200px; background: {{ $style['grad'] }}; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid rgba(139,92,246,0.08);">
                    {{-- Decorative pattern --}}
                    <div style="position: absolute; inset: 0; opacity: 0.05; background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.5) 1px, transparent 0); background-size: 24px 24px;"></div>
                    
                    <div style="text-align: center; position: relative; z-index: 1;">
                        <i data-lucide="{{ $style['icon'] }}" style="width: 64px; height: 64px; color: {{ $style['text'] }}; opacity: 0.8; filter: drop-shadow(0 0 15px {{ $style['text'] }}44);"></i>
                        <div style="margin-top: 12px; font-size: 0.65rem; color: {{ $style['text'] }}; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase;">
                            {{ $exercise->muscle_group }} Focus
                        </div>
                    </div>
                    
                    {{-- Favorite Button Overlay --}}
                    <button onclick="toggleFavorite(this, '{{ (string)$exercise->id }}')" class="favorite-btn" style="position: absolute; top: 16px; right: 16px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); width: 42px; height: 42px; border-radius: 14px; color: #fff; cursor: pointer; backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        <i data-lucide="bookmark" style="width: 20px; height: 20px;"></i>
                    </button>

                    <div style="position: absolute; bottom: 16px; left: 16px; display: flex; gap: 8px;">
                        <span style="background: rgba(0,0,0,0.6); color: #fff; border: 1px solid rgba(255,255,255,0.1); padding: 5px 12px; border-radius: 10px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; backdrop-filter: blur(8px);">
                            {{ $exercise->equipment }}
                        </span>
                    </div>
                </div>

                <div style="padding: 1.8rem; flex: 1; display: flex; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.2rem; min-height: 54px;">
                        <h3 style="font-size:1.25rem;font-weight:900;color:#fff;margin: 0; line-height: 1.2;">{{ $exercise->name }}</h3>
                        <div style="text-align: right; background: rgba(16,185,129,0.05); padding: 6px 10px; border-radius: 12px; border: 1px solid rgba(16,185,129,0.1);">
                            <span style="display: block; font-size: 0.95rem; font-weight: 900; color: #10b981;">~{{ $calories }}</span>
                            <span style="font-size: 0.6rem; color: rgba(16,185,129,0.6); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">kcal / set</span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; margin-bottom: 2rem;">
                        <div style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.4);">
                            <i data-lucide="bar-chart-2" style="width: 16px; height: 16px;"></i>
                            <span style="font-size: 0.8rem; font-weight: 600;">{{ $exercise->difficulty }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,0.4);">
                            <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                            <span style="font-size: 0.8rem; font-weight: 600;">10-15 min</span>
                        </div>
                    </div>

                    <div style="margin-top: auto; display: flex; flex-direction: column; gap: 12px;">
                        <a href="{{ route('exercises.show', $exercise->id) }}" class="btn-outline" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9rem; padding: 14px; border-radius: 14px; background: rgba(255,255,255,0.03); text-decoration: none;">
                            <i data-lucide="info" style="width: 18px; height: 18px; margin-right: 10px;"></i> View Guide
                        </a>
                        <button onclick="openWorkoutModal('{{ (string)$exercise->id }}')" 
                                class="btn-premium" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9rem; padding: 14px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.1); cursor: pointer;">
                            <i data-lucide="plus" style="width: 18px; height: 18px; margin-right: 10px;"></i> Add to Workout
                        </button>
                    </div>
                </div>
            </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:4rem 2rem;background:rgba(255,255,255,.02);border:1px solid rgba(139,92,246,.15);border-radius:24px;">
            <div style="font-size:4rem;opacity:.3;margin-bottom:1rem;">😢</div>
            <h3 style="font-size:1.3rem;font-weight:800;color:#e2d9f3;margin-bottom:.5rem;">No exercises found</h3>
            <p style="color:rgba(255,255,255,.4);font-size:.9rem;">Try adjusting your filters</p>
        </div>
    @endforelse
</div>

<div style="margin-top:2.5rem;">
    {{ $exercises->links() }}
</div>
</div>

@push('modals')
@if(Auth::user()->role === 'trainer')
<div id="customExerciseModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 500px;">
        <form action="{{ route('exercises.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 900; color: #fff;">Add Custom Exercise ✨</h2>
                    <p style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">Expand your professional library</p>
                </div>
                <button type="button" onclick="closeCustomExerciseModal()" class="modal-close-btn">
                    <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>
            <div class="modal-body" style="display: flex; flex-direction: column; gap: 20px;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: rgba(196,181,253,0.6); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Exercise Name</label>
                    <input type="text" name="name" class="form-input" placeholder="e.g. Diamond Pushups" required 
                           style="width: 100%; padding: 14px 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; color: #fff;">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label style="display: block; font-size: 0.75rem; font-weight: 800; color: rgba(196,181,253,0.6); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Muscle Group</label>
                        <select name="muscle_group" class="form-input" required
                                style="width: 100%; padding: 14px 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; color: #fff; appearance: none;">
                            <option value="Chest">Chest</option>
                            <option value="Back">Back</option>
                            <option value="Legs">Legs</option>
                            <option value="Shoulders">Shoulders</option>
                            <option value="Arms">Arms</option>
                            <option value="Core">Core</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-size: 0.75rem; font-weight: 800; color: rgba(196,181,253,0.6); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Equipment</label>
                        <select name="equipment" class="form-input"
                                style="width: 100%; padding: 14px 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; color: #fff; appearance: none;">
                            <option value="None">None (Bodyweight)</option>
                            <option value="Dumbbell">Dumbbell</option>
                            <option value="Barbell">Barbell</option>
                            <option value="Machine">Machine</option>
                            <option value="Kettlebell">Kettlebell</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: rgba(196,181,253,0.6); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Description</label>
                    <textarea name="description" class="form-input" rows="4" placeholder="Step-by-step instructions..."
                              style="width: 100%; padding: 14px 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; color: #fff; resize: none;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.5rem; display: flex; gap: 12px;">
                <button type="button" onclick="closeCustomExerciseModal()" class="btn-outline" style="flex: 1; padding: 14px; border-radius: 14px; justify-content: center;">Cancel</button>
                <button type="submit" class="btn-premium" style="flex: 2; padding: 14px; border-radius: 14px; justify-content: center; font-weight: 800;">Save Exercise</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Workout Selector Modal --}}
<div id="workoutSelectorModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 450px;">
        <div class="modal-header">
            <div>
                <h2 id="modalTitle" style="font-size: 1.25rem; font-weight: 900; color: #fff;">Assign Exercise</h2>
                <p id="modalSubtitle" style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">Select a target for this exercise</p>
            </div>
            <button type="button" onclick="closeWorkoutModal()" class="modal-close-btn">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="selectedExerciseId">
            <input type="hidden" id="selectedUserId">
            
            {{-- Step 1: Client List (Trainer Only) --}}
            <div id="clientListStep" style="display: none; flex-direction: column; gap: 10px;">
                <label style="font-size: 0.7rem; font-weight: 800; color: rgba(196,181,253,0.5); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 5px;">Select Client</label>
                @foreach($clients ?? [] as $client)
                    <button onclick="selectClient('{{ (string)$client->_id }}', '{{ $client->name }}')" class="btn-outline" style="justify-content: space-between; padding: 14px 18px; border-radius: 16px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 32px; height: 32px; background: rgba(139,92,246,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #c4b5fd;">{{ substr($client->name, 0, 1) }}</div>
                            <span style="font-weight: 700;">{{ $client->name }}</span>
                        </div>
                        <i data-lucide="chevron-right" style="width: 18px; height: 18px; opacity: 0.3;"></i>
                    </button>
                @endforeach
            </div>

            {{-- Step 2: Workout List --}}
            <div id="workoutListStep" style="display: none; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <label id="workoutListLabel" style="font-size: 0.7rem; font-weight: 800; color: rgba(196,181,253,0.5); text-transform: uppercase; letter-spacing: 0.1em;">Select Workout Plan</label>
                    <button id="backToClientsBtn" onclick="showClientStep()" style="background: none; border: none; color: var(--vg-accent); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: none;">← Back</button>
                </div>
                <div id="workoutItems" style="display: flex; flex-direction: column; gap: 8px; max-height: 300px; overflow-y: auto; padding-right: 5px;">
                    {{-- Dynamically populated --}}
                </div>
                <div id="noWorkoutsMsg" style="display: none; text-align: center; padding: 2rem 1rem; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 16px;">
                    <p style="color: rgba(255,255,255,0.4); font-size: 0.85rem; margin: 0;">No active workout plans found.</p>
                </div>
            </div>
            
            <div id="modalLoading" style="display: none; text-align: center; padding: 2rem;">
                <div class="loading-spinner" style="margin: 0 auto 15px;"></div>
                <p style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">Fetching plans...</p>
            </div>
        </div>
    </div>
</div>

@endpush

@push('scripts')
<script>
    let showSavedOnly = false;
    const userRole = "{{ Auth::user()->role }}";
    const currentUserId = "{{ Auth::id() }}";

    function openCustomExerciseModal() {
        document.getElementById('customExerciseModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeCustomExerciseModal() {
        document.getElementById('customExerciseModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // New Multi-Step Flow
    function openWorkoutModal(exerciseId) {
        document.getElementById('selectedExerciseId').value = exerciseId;
        document.getElementById('workoutSelectorModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        if (userRole === 'trainer') {
            showClientStep();
        } else {
            selectClient(currentUserId, 'My Plans');
        }
    }

    function closeWorkoutModal() {
        document.getElementById('workoutSelectorModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function showClientStep() {
        document.getElementById('clientListStep').style.display = 'flex';
        document.getElementById('workoutListStep').style.display = 'none';
        document.getElementById('modalTitle').innerText = 'Assign Exercise';
        document.getElementById('modalSubtitle').innerText = 'Select a client to assign this exercise';
        document.getElementById('backToClientsBtn').style.display = 'none';
    }

    function selectClient(userId, userName) {
        document.getElementById('selectedUserId').value = userId;
        document.getElementById('clientListStep').style.display = 'none';
        document.getElementById('modalLoading').style.display = 'block';
        document.getElementById('workoutListStep').style.display = 'none';
        
        if (userRole === 'trainer') {
            document.getElementById('modalTitle').innerText = 'Assign to ' + userName;
            document.getElementById('backToClientsBtn').style.display = 'block';
        } else {
            document.getElementById('modalTitle').innerText = 'Add to Workout';
        }

        fetch(`/exercises/user-workouts/${userId}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch workouts');
                return response.json();
            })
            .then(workouts => {
                document.getElementById('modalLoading').style.display = 'none';
                document.getElementById('workoutListStep').style.display = 'flex';
                
                const container = document.getElementById('workoutItems');
                container.innerHTML = '';
                
                if (workouts.length === 0) {
                    document.getElementById('noWorkoutsMsg').style.display = 'block';
                } else {
                    document.getElementById('noWorkoutsMsg').style.display = 'none';
                    workouts.forEach(workout => {
                        const btn = document.createElement('button');
                        btn.className = 'btn-outline';
                        btn.style = 'justify-content: space-between; padding: 12px 16px; border-radius: 12px; margin-bottom: 8px; width: 100%; text-align: left;';
                        btn.innerHTML = `
                            <div>
                                <div style="font-weight: 700; color: #fff;">${workout.title}</div>
                                <div style="font-size: 0.7rem; color: rgba(255,255,255,0.3);">${new Date(workout.scheduled_date).toLocaleDateString()}</div>
                            </div>
                            <i data-lucide="plus" style="width: 16px; height: 16px; opacity: 0.5;"></i>
                        `;
                        btn.onclick = () => addToWorkout(workout.id);
                        container.appendChild(btn);
                    });
                    lucide.createIcons();
                }
            })
            .catch(error => {
                console.error(error);
                document.getElementById('modalLoading').style.display = 'none';
                alert('❌ Error fetching workout plans. Please try again.');
                showClientStep();
            });
    }

    function addToWorkout(workoutId) {
        const exerciseId = document.getElementById('selectedExerciseId').value;
        
        fetch('/exercises/add-to-workout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ workout_id: workoutId, exercise_id: exerciseId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✨ ' + data.message);
                closeWorkoutModal();
            } else {
                alert('❌ Error: ' + (data.error || 'Failed to add exercise'));
            }
        });
    }

    function toggleFavorite(btn, exerciseId) {
        let favorites = JSON.parse(localStorage.getItem('vg_favorites') || '[]');
        const icon = btn.querySelector('i');
        
        if (favorites.includes(exerciseId)) {
            favorites = favorites.filter(id => id !== exerciseId);
            btn.style.color = '#fff';
            btn.style.background = 'rgba(0,0,0,0.5)';
            icon.setAttribute('data-lucide', 'bookmark');
        } else {
            favorites.push(exerciseId);
            btn.style.color = '#fbbf24';
            btn.style.background = 'rgba(251,191,36,0.15)';
            icon.setAttribute('data-lucide', 'bookmark');
            btn.classList.add('is-favorite');
        }
        
        localStorage.setItem('vg_favorites', JSON.stringify(favorites));
        lucide.createIcons();
        
        if (showSavedOnly) filterSaved(document.getElementById('savedFilterBtn'), true);
    }

    function filterSaved(btn, refreshOnly = false) {
        if (!refreshOnly) {
            showSavedOnly = !showSavedOnly;
            btn.classList.toggle('active-filter');
            btn.style.background = showSavedOnly ? 'rgba(139,92,246,0.2)' : 'transparent';
            btn.style.borderColor = showSavedOnly ? 'rgba(139,92,246,0.4)' : 'rgba(255,255,255,0.1)';
        }

        const favorites = JSON.parse(localStorage.getItem('vg_favorites') || '[]');
        const cards = document.querySelectorAll('.exercise-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const id = card.getAttribute('data-exercise-id');
            if (showSavedOnly) {
                if (favorites.includes(id)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            } else {
                card.style.display = 'flex';
                visibleCount++;
            }
        });
        
        const emptyState = document.getElementById('favoritesEmptyState');
        if (showSavedOnly && visibleCount === 0) {
            if (!emptyState) {
                const grid = document.querySelector('div[style*="grid-template-columns"]');
                const div = document.createElement('div');
                div.id = 'favoritesEmptyState';
                div.style = 'grid-column: 1/-1; text-align: center; padding: 4rem 2rem; background: rgba(255,255,255,0.02); border: 1px dashed rgba(139,92,246,0.3); border-radius: 24px;';
                div.innerHTML = `
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🔖</div>
                    <h3 style="color: #fff; font-weight: 800;">No saved exercises yet</h3>
                    <p style="color: rgba(255,255,255,0.4);">Click the bookmark icon on any exercise to save it here.</p>
                `;
                grid.appendChild(div);
            } else {
                emptyState.style.display = 'block';
            }
        } else if (emptyState) {
            emptyState.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const favorites = JSON.parse(localStorage.getItem('vg_favorites') || '[]');
        document.querySelectorAll('.exercise-card').forEach(card => {
            const id = card.getAttribute('data-exercise-id');
            if (favorites.includes(id)) {
                const btn = card.querySelector('.favorite-btn');
                btn.style.color = '#fbbf24';
                btn.style.background = 'rgba(251,191,36,0.15)';
                btn.classList.add('is-favorite');
            }
        });
    });
</script>

<style>
    .loading-spinner {
        width: 30px;
        height: 30px;
        border: 3px solid rgba(139, 92, 246, 0.1);
        border-top: 3px solid #8b5cf6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    
    .favorite-btn.is-favorite i {
        fill: #fbbf24;
    }
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }
    .modal-content {
        background: #0a0a1a;
        border: 1px solid rgba(139, 92, 246, 0.3);
        border-radius: 28px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid rgba(139, 92, 246, 0.1);
    }
    .modal-close-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .modal-close-btn:hover {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.2);
    }
    .exercise-card:hover {
        transform: translateY(-8px);
        border-color: rgba(139, 92, 246, 0.4) !important;
        box-shadow: 0 20px 40px rgba(139, 92, 246, 0.15);
    }
    #workoutItems::-webkit-scrollbar { width: 5px; }
    #workoutItems::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
    #workoutItems::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.3); border-radius: 10px; }
</style>
@endpush
@endsection