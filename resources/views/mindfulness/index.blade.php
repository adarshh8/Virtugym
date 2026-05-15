@extends('layouts.app')

@section('title', 'Mindfulness & Recovery')

@section('content')
<div style="max-width:1200px;margin:0 auto;">
    <div style="margin-bottom:2rem;" class="fade-in-up">
        <h1 style="font-size:1.8rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
            Mindfulness & Recovery 🧘‍♂️
        </h1>
        <p style="color:var(--vg-text-muted);font-size:.9rem;">Heal your body and mind for sustainable progress</p>
    </div>

    <!-- Categories -->
    <div style="display:flex;gap:1rem;margin-bottom:2rem;overflow-x:auto;padding-bottom:.5rem;" class="fade-in-up delay-1">
        <a href="{{ route('mindfulness.index') }}" 
           style="background:{{ !request('category') ? 'var(--vg-gradient)' : 'var(--vg-panel)' }};color:#fff;padding:.6rem 1.2rem;border-radius:50px;font-size:.85rem;font-weight:700;text-decoration:none;border:1px solid var(--vg-border);white-space:nowrap;">
           All
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('mindfulness.index', ['category' => $cat->category]) }}" 
               style="background:{{ request('category') == $cat->category ? 'var(--vg-gradient)' : 'var(--vg-panel)' }};color:#fff;padding:.6rem 1.2rem;border-radius:50px;font-size:.85rem;font-weight:700;text-decoration:none;border:1px solid var(--vg-border);white-space:nowrap;">
               {{ $cat->category }}
            </a>
        @endforeach
    </div>

    <!-- Content Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem;" class="fade-in-up delay-2">
        @forelse($contents as $item)
            <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;overflow:hidden;transition:all .3s;"
                 onmouseover="this.style.transform='translateY(-5px)';this.style.borderColor='var(--vg-border-strong)';"
                 onmouseout="this.style.transform='';this.style.borderColor='var(--vg-border)';">
                <div style="height:180px;background:url('{{ $item->image_url ?? 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&q=80&w=800' }}');background-size:cover;background-position:center;position:relative;">
                    <div style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,.6);backdrop-filter:blur(10px);padding:4px 10px;border-radius:50px;font-size:.7rem;font-weight:700;color:#fff;">
                        {{ $item->duration_minutes }} min
                    </div>
                </div>
                <div style="padding:1.5rem;">
                    <span style="font-size:.7rem;font-weight:800;color:var(--vg-accent);text-transform:uppercase;letter-spacing:.05em;">{{ $item->category }}</span>
                    <h3 style="font-size:1.1rem;font-weight:800;color:var(--vg-text-strong);margin:.5rem 0 .8rem;">{{ $item->title }}</h3>
                    <p style="color:var(--vg-text-muted);font-size:.85rem;margin-bottom:1.5rem;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $item->description }}
                    </p>
                    <a href="{{ route('mindfulness.show', $item->id) }}" 
                       style="display:block;text-align:center;background:rgba(255,255,255,.05);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:.8rem;border-radius:12px;font-size:.85rem;font-weight:700;text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.background='var(--vg-accent-soft)'"
                       onmouseout="this.style.background='rgba(255,255,255,.05)'">
                        Start Session
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:4rem;background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;">
                <p style="color:var(--vg-text-muted);">No mindfulness content available yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
