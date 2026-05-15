@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div style="max-width:860px;margin:0 auto;">

    <h1 style="font-size:1.6rem;font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:1.8rem;" class="fade-in-up">
        ⚙️ Edit Profile
    </h1>

    {{-- Success Alert --}}
    @if(session('success'))
        <div style="background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.3);border-left:4px solid #10b981;border-radius:12px;padding:14px 18px;color:#6ee7b7;display:flex;align-items:center;gap:10px;margin-bottom:1.5rem;" class="fade-in-up">
            <span style="font-size:1.2rem;">✅</span>
            <p style="font-weight:500;">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ===== PROFILE INFO FORM ===== --}}
    <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;padding:2rem;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.6rem;padding-bottom:1rem;border-bottom:1px solid rgba(139,92,246,.12);">
            <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,rgba(139,92,246,.3),rgba(236,72,153,.2));display:flex;align-items:center;justify-content:center;font-size:1.3rem;">👤</div>
            <div>
                <h2 style="font-size:1.05rem;font-weight:800;color:#e2d9f3;">Profile Information</h2>
                <p style="font-size:.75rem;color:rgba(255,255,255,.3);">Update your personal details and fitness data</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" style="display:flex;flex-direction:column;gap:1.2rem;">
            @csrf
            @method('PATCH')

            {{-- Name & Email --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">FULL NAME</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid {{ $errors->has('name') ? 'rgba(239,68,68,.5)' : 'rgba(139,92,246,.25)' }};border-radius:12px;color:#fff;font-size:.88rem;outline:none;transition:border-color .2s;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)';this.style.background='rgba(139,92,246,.08)'"
                           onblur="this.style.borderColor='rgba(139,92,246,.25)';this.style.background='rgba(255,255,255,.05)'">
                    @error('name')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">EMAIL ADDRESS</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid {{ $errors->has('email') ? 'rgba(239,68,68,.5)' : 'rgba(139,92,246,.25)' }};border-radius:12px;color:#fff;font-size:.88rem;outline:none;transition:border-color .2s;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)';this.style.background='rgba(139,92,246,.08)'"
                           onblur="this.style.borderColor='rgba(139,92,246,.25)';this.style.background='rgba(255,255,255,.05)'">
                    @error('email')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Age / Gender / Weight / Height --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">AGE</label>
                    <input type="number" name="age" value="{{ old('age', $user->age) }}" placeholder="25"
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">GENDER</label>
                    <select name="gender" style="width:100%;padding:11px 14px;background:rgba(8,8,26,.9);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                            onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                        <option value="">Select</option>
                        <option value="male"   {{ old('gender',$user->gender)=='male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender',$user->gender)=='female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender',$user->gender)=='other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">WEIGHT (kg)</label>
                    <input type="number" step=".1" name="weight" value="{{ old('weight', $user->weight) }}" placeholder="70"
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">HEIGHT (cm)</label>
                    <input type="number" name="height" value="{{ old('height', $user->height) }}" placeholder="175"
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                </div>
            </div>

            {{-- Fitness Level / Goal / Workout Days --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">FITNESS LEVEL</label>
                    <select name="fitness_level" style="width:100%;padding:11px 14px;background:rgba(8,8,26,.9);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;">
                        <option value="">Select</option>
                        <option value="beginner"     {{ old('fitness_level',$user->fitness_level)=='beginner'     ? 'selected':'' }}>🌱 Beginner</option>
                        <option value="intermediate" {{ old('fitness_level',$user->fitness_level)=='intermediate' ? 'selected':'' }}>💪 Intermediate</option>
                        <option value="advanced"     {{ old('fitness_level',$user->fitness_level)=='advanced'     ? 'selected':'' }}>🏆 Advanced</option>
                        <option value="expert"       {{ old('fitness_level',$user->fitness_level)=='expert'       ? 'selected':'' }}>⚡ Expert</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">PRIMARY GOAL</label>
                    <select name="goal" style="width:100%;padding:11px 14px;background:rgba(8,8,26,.9);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;">
                        <option value="">Select</option>
                        <option value="weight_loss"    {{ old('goal',$user->goal)=='weight_loss'    ? 'selected':'' }}>🎯 Weight Loss</option>
                        <option value="muscle_gain"    {{ old('goal',$user->goal)=='muscle_gain'    ? 'selected':'' }}>💪 Muscle Gain</option>
                        <option value="endurance"      {{ old('goal',$user->goal)=='endurance'      ? 'selected':'' }}>🏃 Endurance</option>
                        <option value="flexibility"    {{ old('goal',$user->goal)=='flexibility'    ? 'selected':'' }}>🧘 Flexibility</option>
                        <option value="general_fitness"{{ old('goal',$user->goal)=='general_fitness'? 'selected':'' }}>⭐ General Fitness</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">WORKOUT DAYS/WEEK</label>
                    <select name="workout_days" style="width:100%;padding:11px 14px;background:rgba(8,8,26,.9);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;">
                        <option value="">Select</option>
                        @for($i=1;$i<=7;$i++)
                            <option value="{{ $i }}" {{ old('workout_days',$user->workout_days)==$i ? 'selected':'' }}>{{ $i }} day{{ $i>1?'s':'' }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Equipment --}}
            <div>
                <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:8px;">EQUIPMENT AVAILABLE</label>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.5rem;">
                    @foreach(['dumbbells'=>'🏋️ Dumbbells','barbell'=>'🏋️ Barbell','resistance_bands'=>'🎯 Resistance Bands','kettlebells'=>'⚫ Kettlebells','pull_up_bar'=>'🔝 Pull-up Bar','treadmill'=>'🏃 Treadmill'] as $val=>$label)
                        <label style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:rgba(255,255,255,.5);cursor:pointer;padding:8px 12px;background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.15);border-radius:10px;transition:all .2s;"
                               onmouseover="this.style.borderColor='rgba(139,92,246,.4)';this.style.color='#c4b5fd'"
                               onmouseout="this.style.borderColor='rgba(139,92,246,.15)';this.style.color='rgba(255,255,255,.5)'">
                            <input type="checkbox" name="equipment[]" value="{{ $val }}" accent-color="#8b5cf6"
                                   {{ in_array($val, (array)($user->equipment ?? [])) ? 'checked' : '' }}
                                   style="accent-color:#8b5cf6;">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Injuries --}}
            <div>
                <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">INJURIES / LIMITATIONS</label>
                <textarea name="injuries" rows="2" placeholder="List any injuries or physical limitations…"
                          style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;resize:vertical;"
                          onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">{{ old('injuries', $user->injuries) }}</textarea>
            </div>

            @if($user->role === 'trainee')
            <div>
                <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">UPI ID FOR REFUNDS</label>
                <input type="text" name="upi_id" value="{{ old('upi_id', $user->upi_id) }}" placeholder="name@upi"
                       style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid {{ $errors->has('upi_id') ? 'rgba(239,68,68,.5)' : 'rgba(139,92,246,.25)' }};border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                       onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                @error('upi_id')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
            </div>
            @endif

            {{-- Trainer-specific fields --}}
            @if($user->role === 'trainer')
            <div style="border-top:1px solid rgba(139,92,246,.12);padding-top:1.2rem;">
                <p style="font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.1em;margin-bottom:1rem;">TRAINER DETAILS</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                    <div>
                        <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">SPECIALIZATION</label>
                        <input type="text" name="specialization" value="{{ old('specialization',$user->specialization) }}" placeholder="e.g. Weight Loss, HIIT"
                               style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                               onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                    </div>
                    <div>
                        <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">EXPERIENCE (years)</label>
                        <input type="number" name="experience_years" value="{{ old('experience_years',$user->experience_years) }}" placeholder="5"
                               style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                               onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:start;">
                    <div>
                        <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">BIO</label>
                        <textarea name="bio" rows="2" placeholder="Tell clients about yourself…"
                                  style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;resize:vertical;"
                                  onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">{{ old('bio',$user->bio) }}</textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">HOURLY RATE (₹)</label>
                        <input type="number" name="hourly_rate" value="{{ old('hourly_rate',$user->hourly_rate) }}" placeholder="500"
                               style="width:120px;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                               onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                    </div>
                </div>
            </div>
            @endif

            <div>
                <button type="submit"
                        style="background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;border:none;border-radius:12px;padding:13px 32px;font-size:.92rem;font-weight:700;cursor:pointer;box-shadow:0 8px 22px rgba(139,92,246,.35);transition:all .3s;position:relative;overflow:hidden;"
                        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 14px 32px rgba(139,92,246,.55)'"
                        onmouseout="this.style.transform='';this.style.boxShadow='0 8px 22px rgba(139,92,246,.35)'">
                    Save Profile →
                </button>
            </div>
        </form>
    </div>

    {{-- ===== CHANGE PASSWORD FORM ===== --}}
    <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;padding:2rem;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.6rem;padding-bottom:1rem;border-bottom:1px solid rgba(139,92,246,.12);">
            <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,rgba(59,130,246,.3),rgba(99,102,241,.2));display:flex;align-items:center;justify-content:center;font-size:1.3rem;">🔒</div>
            <div>
                <h2 style="font-size:1.05rem;font-weight:800;color:#e2d9f3;">Change Password</h2>
                <p style="font-size:.75rem;color:rgba(255,255,255,.3);">Use a strong, unique password for your account</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.password') }}" style="display:flex;flex-direction:column;gap:1.1rem;">
            @csrf

            <div>
                <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">CURRENT PASSWORD</label>
                <input type="password" name="current_password"
                       style="width:100%;max-width:420px;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid {{ $errors->has('current_password') ? 'rgba(239,68,68,.5)' : 'rgba(139,92,246,.25)' }};border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                       onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'"
                       placeholder="••••••••">
                @error('current_password')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;max-width:840px;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">NEW PASSWORD</label>
                    <input type="password" name="password"
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid {{ $errors->has('password') ? 'rgba(239,68,68,.5)' : 'rgba(139,92,246,.25)' }};border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'"
                           placeholder="Min. 8 characters">
                    @error('password')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">CONFIRM NEW PASSWORD</label>
                    <input type="password" name="password_confirmation"
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'"
                           placeholder="Repeat new password">
                </div>
            </div>

            <div>
                <button type="submit"
                        style="background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff;border:none;border-radius:12px;padding:13px 32px;font-size:.92rem;font-weight:700;cursor:pointer;box-shadow:0 8px 22px rgba(59,130,246,.3);transition:all .3s;"
                        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 14px 32px rgba(59,130,246,.5)'"
                        onmouseout="this.style.transform='';this.style.boxShadow='0 8px 22px rgba(59,130,246,.3)'">
                    Change Password 🔒
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
