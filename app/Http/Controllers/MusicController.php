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
        $hasBookedSession = $this->hasConfirmedBooking();
        $youtubeConfigured = (bool) config('services.youtube.key');

        return view('music.index', compact('hasBookedSession', 'youtubeConfigured'));
    }

    public function search(Request $request)
    {
        if (!$this->hasConfirmedBooking()) {
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
            $response = Http::connectTimeout(3)
                ->timeout(5)
                ->get('https://www.googleapis.com/youtube/v3/search', [
                    'part' => 'snippet',
                    'type' => 'video',
                    'videoEmbeddable' => 'true',
                    'videoSyndicated' => 'true',
                    'order' => 'relevance',
                    'safeSearch' => 'none',
                    'maxResults' => 8,
                    'q' => $validated['q'],
                    'key' => $apiKey,
                ]);
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

    private function hasConfirmedBooking(): bool
    {
        $userId = Auth::id();

        return Booking::where('status', 'confirmed')
            ->where('trainee_id', $userId)
            ->exists()
            || Booking::where('status', 'confirmed')
                ->where('trainer_id', $userId)
                ->exists();
    }
}
