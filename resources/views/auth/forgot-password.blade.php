@extends('layouts.guest')

@section('content')
<div>
    <div class="badge"><span class="bdot"></span> Password Help</div>
    <div class="form-title">Forgot Password</div>
    <div class="form-sub">Get a 6-digit OTP to change your password</div>

    @if (session('status'))
        <div style="background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);color:#86efac;border-radius:12px;padding:.85rem 1rem;font-size:.82rem;margin-bottom:1rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" style="display:flex;flex-direction:column;gap:1rem;">
        @csrf

        <div>
            <label class="lbl">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="inp" placeholder="john@example.com">
            @error('email')<p class="err">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn-go" style="margin-top:.3rem;">Send OTP</button>

        <p style="text-align:center;font-size:.83rem;color:rgba(255,255,255,.35);">
            Remembered it? <a href="{{ route('login') }}" class="link-a">Sign In</a>
        </p>
    </form>
</div>
@endsection
