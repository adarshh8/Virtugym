@extends('layouts.app')

@section('title', 'Workout Music')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Workout Music</h1>
            <p class="text-gray-400 mt-2">Search YouTube and play tracks while you train.</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            My Sessions
        </a>
    </div>

    @if(!$hasBookedSession)
        <div class="bg-white rounded-xl shadow-lg p-10 text-center">
            <div class="text-5xl mb-4">🎵</div>
            <h2 class="text-xl font-bold text-gray-800">Book a session to unlock workout music</h2>
            <p class="text-gray-600 mt-2">Music search is available for users with a confirmed trainer session.</p>
            @if(Auth::user()->role === 'trainee')
                <a href="{{ route('trainee.trainers') }}" class="inline-block mt-5 bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                    Browse Trainers
                </a>
            @endif
        </div>
    @elseif(!$youtubeConfigured)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 rounded-xl p-6">
            <h2 class="font-bold text-lg">YouTube API key needed</h2>
            <p class="mt-2">Add <code class="bg-yellow-100 px-2 py-1 rounded">YOUTUBE_API_KEY</code> to your <code class="bg-yellow-100 px-2 py-1 rounded">.env</code> file, then clear config cache if needed.</p>
        </div>
    @else
        <div class="grid lg:grid-cols-[1.1fr_.9fr] gap-6">
            <section class="bg-white rounded-xl shadow-lg p-6">
                <form id="musicSearchForm" class="flex gap-3">
                    <input id="musicQuery" type="search" placeholder="Search songs, artists, workout mixes..."
                           class="flex-1 px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    <button id="musicSearchButton" type="submit" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition disabled:opacity-60 disabled:cursor-not-allowed">
                        Search
                    </button>
                </form>

                <p id="musicStatus" class="text-sm text-gray-500 mt-4">Try “workout motivation”, “lofi cardio”, or your favorite artist.</p>

                <div id="songResults" class="mt-5 space-y-3"></div>
            </section>

            <aside class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-4 text-white">
                    <p class="text-sm opacity-80">Now Playing</p>
                    <h2 id="nowPlayingTitle" class="font-bold text-lg mt-1">Choose a song</h2>
                    <p id="nowPlayingChannel" class="text-sm opacity-80"></p>
                </div>
                <div class="aspect-video bg-gray-900">
                    <iframe id="youtubePlayer" class="w-full h-full border-0"
                            title="YouTube music player"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen></iframe>
                </div>
                <div class="p-4 text-sm text-gray-500">
                    Songs play through YouTube embeds, so playback follows YouTube availability and ad rules.
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
    }

    function renderSongs(songs) {
        results.innerHTML = '';

        if (!songs.length) {
            statusText.textContent = 'No songs found. Try a different search.';
            return;
        }

        statusText.textContent = `${songs.length} result${songs.length === 1 ? '' : 's'} found`;

        songs.forEach((song, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'w-full flex items-center gap-4 text-left border rounded-xl p-3 hover:border-purple-400 hover:bg-purple-50 transition';
            button.innerHTML = `
                <img src="${song.thumbnail || ''}" alt="" class="w-24 h-16 object-cover rounded-lg bg-gray-200">
                <span class="flex-1 min-w-0">
                    <span class="block font-semibold text-gray-800">${escapeHtml(song.title)}</span>
                    <span class="block text-sm text-gray-500 mt-1">${escapeHtml(song.channel)}</span>
                </span>
                <span class="bg-purple-600 text-white px-3 py-2 rounded-lg text-sm font-semibold">Play</span>
            `;
            button.addEventListener('click', () => playSong(song));
            results.appendChild(button);

            if (index === 0) {
                playSong(song);
            }
        });
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const term = query.value.trim();

        if (term.length < 2) {
            statusText.textContent = 'Enter at least 2 characters.';
            return;
        }

        const searchId = ++activeSearch;
        statusText.textContent = 'Searching YouTube...';
        results.innerHTML = '';
        searchButton.disabled = true;
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

            statusText.textContent = error.name === 'AbortError'
                ? 'Search took too long. Please try again or check your YouTube API key.'
                : error.message;
        } finally {
            clearTimeout(timeout);
            if (searchId === activeSearch) {
                searchButton.disabled = false;
                searchButton.textContent = 'Search';
            }
        }
    });
</script>
@endif
@endsection
