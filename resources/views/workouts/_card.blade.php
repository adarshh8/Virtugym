<div class="workout-card" data-type="{{ strtolower($workout->type) }}" data-id="{{ $workout->id }}">
    <div>
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
            <div style="display:flex;gap:8px;align-items:center;">
                <span class="difficulty-badge diff-{{ strtolower($workout->difficulty) }}">{{ $workout->difficulty }}</span>
                
                {{-- Assigned Status Badge (Trainer only) --}}
                @if(Auth::user()->role === 'trainer')
                    @if($workout->trainee_id)
                        <span style="font-size:0.65rem;font-weight:700;color:#10b981;background:rgba(16,185,129,0.1);padding:4px 10px;border-radius:50px;border:1px solid rgba(16,185,129,0.2);">
                            Assigned to {{ $workout->trainee->name ?? 'Client' }}
                        </span>
                    @else
                        <span style="font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.4);background:rgba(255,255,255,0.05);padding:4px 10px;border-radius:50px;border:1px solid rgba(255,255,255,0.1);">
                            Unassigned
                        </span>
                    @endif
                @endif
            </div>

            @if($showActions ?? true)
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('workouts.edit', $workout->id) }}" style="color: rgba(255,255,255,0.3); transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.3)'">
                    <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i>
                </a>
                <form id="delete-form-{{ $workout->id }}" action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="display: none;">
                    @csrf @method('DELETE')
                </form>
                <button type="button" style="background: none; border: none; padding: 0; color: rgba(255,255,255,0.3); cursor: pointer; transition: color 0.2s;" onclick="deleteWorkout('{{ $workout->id }}')" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='rgba(255,255,255,0.3)'">
                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                </button>
            </div>
            @endif
        </div>
        
        <h4 style="font-size: 1.15rem; font-weight: 800; color: #fff; margin-bottom: 6px;">{{ $workout->title }}</h4>
        <p style="color: rgba(255,255,255,0.4); font-size: 0.8rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="target" style="width: 14px; height: 14px;"></i>
            @php
                $muscles = [];
                if(is_array($workout->exercises)) {
                    foreach($workout->exercises as $ex) {
                        $exercise = \App\Models\Exercise::find($ex['exercise_id']);
                        if($exercise && !in_array($exercise->muscle_group, $muscles)) $muscles[] = $exercise->muscle_group;
                    }
                }
                echo !empty($muscles) ? implode(', ', array_slice($muscles, 0, 3)) . (count($muscles) > 3 ? '...' : '') : 'General Fitness';
            @endphp
        </p>
    </div>

    <div>
        <div style="display: flex; gap: 15px; margin-bottom: 1.2rem; padding-top: 1rem; border-top: 1px solid var(--glass-border);">
            <div style="display: flex; align-items: center; gap: 5px;">
                <i data-lucide="clock" style="width: 14px; height: 14px; color: rgba(255,255,255,0.4);"></i>
                <span style="font-size: 0.8rem; color: #fff; font-weight: 600;">{{ $workout->duration_minutes ?? '45' }}m</span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <i data-lucide="dumbbell" style="width: 14px; height: 14px; color: rgba(255,255,255,0.4);"></i>
                <span style="font-size: 0.8rem; color: #fff; font-weight: 600;">{{ count($workout->exercises ?? []) }} exercises</span>
            </div>
        </div>
        
        <a href="{{ route('workouts.show', $workout->id) }}" class="btn-outline" style="width: 100%; justify-content: center; background: rgba(139, 92, 246, 0.08); border-color: rgba(139, 92, 246, 0.2); color: #a78bfa;">
            Start Workout <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        </a>
    </div>
</div>
