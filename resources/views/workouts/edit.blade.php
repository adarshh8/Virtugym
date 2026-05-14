@extends('layouts.app')

@section('content')
<div class="container py-12">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('workouts.index') }}" class="icon-btn" title="Back to Workouts">
                <i data-lucide="arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-white">Edit Workout</h1>
                <p class="text-white/60">Modify your training routine: {{ $workout->title }}</p>
            </div>
        </div>

        <div class="glass-card p-8">
            <form action="{{ route('workouts.update', $workout->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">Workout Title</label>
                        <input type="text" name="title" class="form-input" value="{{ $workout->title }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Difficulty</label>
                        <select name="difficulty" class="form-input">
                            <option value="Beginner" {{ $workout->difficulty == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="Intermediate" {{ $workout->difficulty == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="Advanced" {{ $workout->difficulty == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-input">
                            <option value="Strength" {{ $workout->type == 'Strength' ? 'selected' : '' }}>Strength</option>
                            <option value="Cardio" {{ $workout->type == 'Cardio' ? 'selected' : '' }}>Cardio</option>
                            <option value="HIIT" {{ $workout->type == 'HIIT' ? 'selected' : '' }}>HIIT</option>
                            <option value="Flexibility" {{ $workout->type == 'Flexibility' ? 'selected' : '' }}>Flexibility</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Duration (mins)</label>
                        <input type="number" name="duration_minutes" class="form-input" value="{{ $workout->duration_minutes }}">
                    </div>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="submit" class="btn-premium flex-1 justify-center">Save Changes</button>
                    <a href="{{ route('workouts.index') }}" class="btn-outline flex-1 justify-center">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
    }

    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: rgba(255,255,255,0.6); margin-bottom: 8px; }
    .form-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 12px 16px;
        color: #fff;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    .form-input:focus {
        background: rgba(255, 255, 255, 0.07);
        border-color: #a78bfa;
        outline: none;
        box-shadow: 0 0 0 4px rgba(167, 139, 250, 0.1);
    }

    .btn-premium {
        background: linear-gradient(135deg, #a78bfa 0%, #ec4899 100%);
        color: white;
        padding: 14px 24px;
        border-radius: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(167, 139, 250, 0.4);
    }

    .btn-outline {
        background: rgba(255, 255, 255, 0.05);
        color: white;
        padding: 14px 24px;
        border-radius: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .btn-outline:hover { background: rgba(255, 255, 255, 0.1); }

    .icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .icon-btn:hover {
        background: rgba(255,255,255,0.1);
        color: #fff;
        border-color: rgba(255,255,255,0.2);
    }
</style>
@endsection
