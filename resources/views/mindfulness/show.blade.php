@extends('layouts.app')

@section('title', $content->title)

@section('content')
<div style="max-width:900px;margin:0 auto;">
    <div style="margin-bottom:2rem;" class="fade-in-up">
        <a href="{{ route('mindfulness.index') }}" style="color:var(--vg-text-muted);text-decoration:none;font-size:.85rem;display:flex;align-items:center;gap:6px;margin-bottom:1rem;">
            <i data-lucide="arrow-left" style="width:16px;"></i> Back to Library
        </a>
        <h1 style="font-size:2.2rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
            {{ $content->title }}
        </h1>
        <div style="display:flex;align-items:center;gap:12px;color:var(--vg-text-muted);font-size:.9rem;">
            <span style="color:var(--vg-accent);font-weight:700;">{{ $content->category }}</span>
            <span>•</span>
            <span>{{ $content->duration_minutes }} minutes</span>
        </div>
    </div>

    @if($content->media_url)
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;overflow:hidden;margin-bottom:2rem;" class="fade-in-up delay-1">
            @if(Str::contains($content->media_url, ['youtube.com', 'youtu.be']))
                @php 
                    $videoId = '';
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $content->media_url, $match)) {
                        $videoId = $match[1];
                    }
                @endphp
                <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
                    <iframe style="position:absolute;top:0;left:0;width:100%;height:100%;" 
                            src="https://www.youtube.com/embed/{{ $videoId }}" 
                            frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
                    </iframe>
                </div>
            @else
                <video src="{{ $content->media_url }}" controls style="width:100%;display:block;"></video>
            @endif
        </div>
    @endif

    <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:2.5rem;" class="fade-in-up delay-2">
        <div style="color:var(--vg-text-strong);line-height:1.7;font-size:1.05rem;">
            {!! nl2br(e($content->content)) !!}
        </div>
    </div>
</div>
@endsection
