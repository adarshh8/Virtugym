<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class MusicController extends Controller
{
    public function index()
    {
        $hasBookedSession = $this->hasConfirmedTraineeBooking();
        $youtubeConfigured = (bool) config('services.youtube.key');

        return view('music.index', compact('hasBookedSession', 'youtubeConfigured'));
    }

    public function search(Request $request)
    {
        if (!$this->hasConfirmedTraineeBooking()) {
            abort(403, 'Workout music is available after booking a confirmed session.');
        }

        $validated = $request->validate([
            'q' => 'required|string|min:2|max:80',
        ]);

        $apiKey = config('services.youtube.key');

        if (!$apiKey) {
            return response()->json([
                'message' => 'YouTube API key is not configured.',
            ], 422);
        }

        try {
            $response = $this->youtubeSearch($validated['q'], 8, $apiKey);
        } catch (ConnectionException|RequestException $exception) {
            return response()->json([
                'message' => 'YouTube search timed out. Please try again.',
            ], 504);
        }

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Could not search YouTube right now. Please try again.',
            ], $response->status() >= 400 ? 502 : 500);
        }

        $songs = collect($response->json('items', []))
            ->filter(fn ($item) => !empty($item['id']['videoId']))
            ->map(function ($item) {
                return [
                    'video_id' => $item['id']['videoId'],
                    'title' => $item['snippet']['title'] ?? 'Untitled video',
                    'channel' => $item['snippet']['channelTitle'] ?? 'YouTube',
                    'thumbnail' => $item['snippet']['thumbnails']['medium']['url']
                        ?? $item['snippet']['thumbnails']['default']['url']
                        ?? null,
                ];
            })
            ->values();

        return response()->json(['songs' => $songs]);
    }

    public function defaultTrack()
    {
        if (!$this->hasConfirmedTraineeBooking()) {
            abort(403, 'Workout music is available after booking a confirmed session.');
        }

        $apiKey = config('services.youtube.key');

        if (!$apiKey) {
            return response()->json([
                'message' => 'YouTube API key is not configured.',
            ], 422);
        }

        try {
            $response = $this->youtubeSearch('gym workout music motivation clean', 1, $apiKey);
        } catch (ConnectionException|RequestException $exception) {
            return response()->json([
                'message' => 'Could not load background music.',
            ], 504);
        }

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Could not load background music.',
            ], 502);
        }

        $item = collect($response->json('items', []))->firstWhere('id.videoId');

        if (!$item) {
            return response()->json([
                'message' => 'No background music track found.',
            ], 404);
        }

        return response()->json([
            'song' => [
                'video_id' => $item['id']['videoId'],
                'title' => $item['snippet']['title'] ?? 'Workout music',
                'channel' => $item['snippet']['channelTitle'] ?? 'YouTube',
            ],
        ]);
    }

    public function backgroundTrack()
    {
        $apiKey = config('services.youtube.key');
        $fallbackVideoId = config('services.youtube.background_video_id');

        if (!$apiKey) {
            return response()->json([
                'song' => [
                    'video_id' => $fallbackVideoId,
                    'title' => 'VirtuGym background workout music',
                    'channel' => 'YouTube',
                ],
            ]);
        }

        try {
            $response = $this->youtubeSearch('gym workout music motivation clean no copyright', 1, $apiKey);
        } catch (ConnectionException|RequestException $exception) {
            return response()->json([
                'song' => [
                    'video_id' => $fallbackVideoId,
                    'title' => 'VirtuGym background workout music',
                    'channel' => 'YouTube',
                ],
            ]);
        }

        if (!$response->successful()) {
            return response()->json([
                'song' => [
                    'video_id' => $fallbackVideoId,
                    'title' => 'VirtuGym background workout music',
                    'channel' => 'YouTube',
                ],
            ]);
        }

        $item = collect($response->json('items', []))->firstWhere('id.videoId');

        return response()->json([
            'song' => [
                'video_id' => $item['id']['videoId'] ?? $fallbackVideoId,
                'title' => $item['snippet']['title'] ?? 'VirtuGym background workout music',
                'channel' => $item['snippet']['channelTitle'] ?? 'YouTube',
            ],
        ]);
    }

    private function youtubeSearch(string $query, int $maxResults, string $apiKey)
    {
        return Http::connectTimeout(3)
            ->timeout(5)
            ->get('https://www.googleapis.com/youtube/v3/search', [
                'part' => 'snippet',
                'type' => 'video',
                'videoEmbeddable' => 'true',
                'videoSyndicated' => 'true',
                'order' => 'relevance',
                'safeSearch' => 'none',
                'maxResults' => $maxResults,
                'q' => $query,
                'key' => $apiKey,
            ]);
    }

    private function hasConfirmedTraineeBooking(): bool
    {
        $userId = Auth::id();

        if (Auth::user()->role !== 'trainee') {
            return false;
        }

        return Booking::where('status', 'confirmed')
            ->where('trainee_id', $userId)
            ->exists();
    }
}
