@extends('layouts.guest')

@section('content')
<div>
    <div class="badge"><span class="bdot"></span> OTP Verification</div>
    <div class="form-title">Change Password</div>
    <div class="form-sub">Enter the OTP sent to your email</div>

    @if (session('status'))
        <div style="background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);color:#86efac;border-radius:12px;padding:.85rem 1rem;font-size:.82rem;margin-bottom:1rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" style="display:flex;flex-direction:column;gap:1rem;">
        @csrf

        <div>
            <label class="lbl">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                   class="inp" placeholder="john@example.com" autocomplete="username">
            @error('email')<p class="err">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="lbl">6-Digit OTP</label>
            <input id="otp" type="text" name="otp" value="{{ old('otp') }}" required
                   inputmode="numeric" pattern="[0-9]{6}" maxlength="6" class="inp" placeholder="123456">
            @error('otp')<p class="err">{{ $message }}</p>@enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem;">
            <div>
                <label class="lbl">New Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       class="inp" placeholder="••••••••">
                @error('password')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="lbl">Confirm</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="inp" placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="btn-go" style="margin-top:.3rem;">Change Password</button>

        <p style="text-align:center;font-size:.83rem;color:rgba(255,255,255,.35);">
            Need a new code? <a href="{{ route('password.request') }}" class="link-a">Send OTP again</a>
        </p>
    </form>
</div>
@endsection
