@extends('layouts.app')

@section('title', 'Workout Music')

@section('content')
<div class="layout-container" style="max-width:1200px;margin:0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2.5rem;flex-wrap:wrap;gap:1.5rem;" class="fade-in-up">
        <div>
            <h1 style="font-size:2.2rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.4rem;">Workout Music 🎵</h1>
            <p style="color:var(--vg-text-muted);font-size:.9rem;">Search YouTube and play high-energy tracks while you train.</p>
        </div>
        <a href="{{ route('bookings.index') }}" style="background:var(--vg-panel);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:10px 20px;border-radius:12px;font-size:.85rem;font-weight:600;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--vg-accent-soft)'" onmouseout="this.style.background='var(--vg-panel)'">
            My Sessions
        </a>
    </div>

    @if(!$hasBookedSession)
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:4rem 2rem;text-align:center;" class="fade-in-up delay-1">
            <div style="font-size:4rem;margin-bottom:1.5rem;filter:drop-shadow(0 0 15px var(--vg-accent-glow));">🎸</div>
            <h2 style="font-size:1.5rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:1rem;">Book a session to unlock workout music</h2>
            <p style="color:var(--vg-text-muted);font-size:1rem;max-width:500px;margin:0 auto 2rem;">Music search is available for users with a confirmed trainer session to keep the energy high during workouts.</p>
            @if(Auth::user()->role === 'trainee')
                <a href="{{ route('trainee.trainers') }}" style="display:inline-block;background:var(--vg-gradient);color:#fff;padding:12px 32px;border-radius:12px;font-weight:700;text-decoration:none;box-shadow:0 8px 25px var(--vg-accent-glow);transition:all .2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    Browse Trainers
                </a>
            @endif
        </div>
    @elseif(!$youtubeConfigured)
        <div style="background:rgba(245,158,11,.1);border-left:4px solid #f59e0b;padding:1.5rem;border-radius:16px;color:#fcd34d;" class="fade-in-up delay-1">
            <h2 style="font-weight:800;font-size:1.1rem;margin-bottom:.5rem;">YouTube API key needed</h2>
            <p style="font-size:.9rem;opacity:.9;">Add <code>YOUTUBE_API_KEY</code> to your <code>.env</code> file to enable music searching.</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:minmax(0, 1.1fr) minmax(0, 0.9fr);gap:2rem;" class="music-layout">
            <style>
                @media(max-width: 992px) { .music-layout { grid-template-columns: 1fr !important; } }
                .search-input {
                    background: var(--vg-sidebar) !important;
                    border: 1px solid var(--vg-border) !important;
                    color: var(--vg-text-strong) !important;
                }
                .search-input:focus {
                    border-color: var(--vg-accent) !important;
                    box-shadow: 0 0 15px var(--vg-accent-glow) !important;
                }
                .song-item {
                    background: var(--vg-panel);
                    border: 1px solid var(--vg-border);
                    transition: all .2s;
                }
                .song-item:hover {
                    border-color: var(--vg-accent);
                    background: var(--vg-accent-soft);
                    transform: translateX(5px);
                }
            </style>
            
            <section style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:2rem;min-width:0;" class="fade-in-up delay-1">
                <form id="musicSearchForm" style="display:flex;gap:12px;margin-bottom:1.5rem;">
                    <input id="musicQuery" type="search" placeholder="Search songs, artists, workout mixes..."
                           class="search-input" style="flex:1;padding:14px 20px;border-radius:14px;outline:none;font-size:.95rem;" required>
                    <button id="musicSearchButton" type="submit" style="background:var(--vg-gradient);color:#fff;padding:0 24px;border-radius:14px;font-weight:700;cursor:pointer;border:none;box-shadow:0 4px 15px var(--vg-accent-glow);transition:all .2s;">
                        Search
                    </button>
                </form>

                <p id="musicStatus" style="font-size:.8rem;color:var(--vg-text-muted);margin-bottom:2rem;display:flex;align-items:center;gap:6px;">
                    <i data-lucide="info" style="width:14px;height:14px;"></i>
                    Try “workout motivation”, “lofi cardio”, or your favorite artist.
                </p>

                <div id="songResults" style="display:flex;flex-direction:column;gap:12px;"></div>
            </section>

            <aside style="position:sticky;top:2rem;height:fit-content;min-width:0;" class="fade-in-up delay-2">
                <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,.3);">
                    <div style="background:var(--vg-gradient);padding:1.5rem;color:#fff;">
                        <p style="font-size:.7rem;opacity:.8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:4px;">Now Playing</p>
                        <h2 id="nowPlayingTitle" style="font-weight:800;font-size:1.2rem;line-height:1.3;">Choose a track</h2>
                        <p id="nowPlayingChannel" style="font-size:.8rem;opacity:.8;margin-top:2px;"></p>
                    </div>
                    <div style="aspect-ratio:16/9;background:#000;display:flex;align-items:center;justify-content:center;">
                        <iframe id="youtubePlayer" style="width:100%;height:100%;border:0;"
                                title="YouTube music player"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen></iframe>
                    </div>
                    <div style="padding:1.2rem;font-size:.75rem;color:var(--vg-text-muted);line-height:1.5;background:rgba(0,0,0,.2);">
                        <div style="display:flex;gap:8px;align-items:flex-start;">
                            <i data-lucide="youtube" style="width:16px;height:16px;color:#ef4444;flex-shrink:0;"></i>
                            <span>Playback follows YouTube availability. Search results are provided via YouTube Data API.</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    @endif
</div>

@if($hasBookedSession && $youtubeConfigured)
<script>
    const form = document.getElementById('musicSearchForm');
    const query = document.getElementById('musicQuery');
    const searchButton = document.getElementById('musicSearchButton');
    const results = document.getElementById('songResults');
    const statusText = document.getElementById('musicStatus');
    const player = document.getElementById('youtubePlayer');
    const nowTitle = document.getElementById('nowPlayingTitle');
    const nowChannel = document.getElementById('nowPlayingChannel');
    let activeSearch = 0;

    function escapeHtml(value) {
        const node = document.createElement('div');
        node.textContent = value || '';
        return node.innerHTML;
    }

    function playSong(song) {
        const origin = encodeURIComponent(window.location.origin);
        player.src = `https://www.youtube.com/embed/${song.video_id}?autoplay=1&rel=0&enablejsapi=1&origin=${origin}`;
        nowTitle.textContent = song.title;
        nowChannel.textContent = song.channel;
        
        // Scroll player into view on mobile
        if (window.innerWidth < 992) {
            player.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function renderSongs(songs) {
        results.innerHTML = '';

        if (!songs.length) {
            statusText.innerHTML = '<i data-lucide="alert-circle" style="width:14px;height:14px;"></i> No songs found. Try a different search.';
            lucide.createIcons();
            return;
        }

        statusText.innerHTML = `<i data-lucide="check-circle" style="width:14px;height:14px;"></i> ${songs.length} result${songs.length === 1 ? '' : 's'} found`;
        lucide.createIcons();

        songs.forEach((song, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'song-item';
            button.style.width = '100%';
            button.style.display = 'flex';
            button.style.alignItems = 'center';
            button.style.gap = '1rem';
            button.style.padding = '12px';
            button.style.borderRadius = '16px';
            button.style.cursor = 'pointer';
            button.style.textAlign = 'left';
            button.style.color = 'var(--vg-text-strong)';
            
            button.innerHTML = `
                <img src="${song.thumbnail || ''}" alt="" style="width:80px;height:50px;object-fit:cover;border-radius:8px;background:rgba(255,255,255,.05);">
                <div style="flex:1;min-width:0;">
                    <span style="display:block;font-weight:700;font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(song.title)}</span>
                    <span style="display:block;font-size:.75rem;color:var(--vg-text-muted);margin-top:2px;">${escapeHtml(song.channel)}</span>
                </div>
                <div style="background:var(--vg-accent-soft);color:var(--vg-accent);width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-lucide="play" style="width:14px;height:14px;fill:currentColor;"></i>
                </div>
            `;
            button.addEventListener('click', () => playSong(song));
            results.appendChild(button);

            if (index === 0) {
                playSong(song);
            }
        });
        lucide.createIcons();
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const term = query.value.trim();

        if (term.length < 2) {
            statusText.textContent = 'Enter at least 2 characters.';
            return;
        }

        const searchId = ++activeSearch;
        statusText.innerHTML = '<i data-lucide="loader" class="animate-spin" style="width:14px;height:14px;"></i> Searching YouTube...';
        lucide.createIcons();
        results.innerHTML = '';
        searchButton.disabled = true;
        searchButton.style.opacity = '0.7';
        searchButton.textContent = 'Searching...';

        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 7000);

        try {
            const response = await fetch(`{{ route('music.search') }}?q=${encodeURIComponent(term)}`, {
                headers: { 'Accept': 'application/json' },
                signal: controller.signal
            });
            const data = await response.json().catch(() => ({}));

            if (searchId !== activeSearch) {
                return;
            }

            if (!response.ok) {
                throw new Error(data.message || 'Search failed.');
            }

            renderSongs(data.songs || []);
        } catch (error) {
            if (searchId !== activeSearch) {
                return;
            }

            statusText.innerHTML = `<i data-lucide="alert-triangle" style="width:14px;height:14px;"></i> ${error.name === 'AbortError' ? 'Search took too long.' : error.message}`;
            lucide.createIcons();
        } finally {
            clearTimeout(timeout);
            if (searchId === activeSearch) {
                searchButton.disabled = false;
                searchButton.style.opacity = '1';
                searchButton.textContent = 'Search';
            }
        }
    });
    
    // Initial icons
    window.addEventListener('DOMContentLoaded', () => {
        if(typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endif
@endsection
