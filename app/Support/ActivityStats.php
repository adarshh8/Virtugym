<?php

namespace App\Support;

use App\Models\ExerciseLog;
use App\Models\ProgressMetric;
use App\Models\User;
use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ActivityStats
{
    public static function recordDailyVisit(User $user): void
    {
        $today = now()->toDateString();
        $dates = collect($user->activity_visit_dates ?? [])
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->values();

        if ($dates->contains($today)) {
            return;
        }

        $user->activity_visit_dates = $dates
            ->push($today)
            ->sort()
            ->take(-370)
            ->values()
            ->all();
        $user->save();
    }

    public static function forUser(string $userId, int $days = 91): array
    {
        $end = now()->endOfDay();
        $start = now()->subDays($days - 1)->startOfDay();
        $dailyCounts = [];
        $latestActivityAt = null;

        self::collectWorkoutActivity($userId, $start, $end)->each(function (Carbon $date) use (&$dailyCounts, &$latestActivityAt) {
            self::addActivity($dailyCounts, $latestActivityAt, $date, 2);
        });

        self::collectExerciseActivity($userId, $start, $end)->each(function (Carbon $date) use (&$dailyCounts, &$latestActivityAt) {
            self::addActivity($dailyCounts, $latestActivityAt, $date, 1);
        });

        self::collectProgressActivity($userId, $start, $end)->each(function (Carbon $date) use (&$dailyCounts, &$latestActivityAt) {
            self::addActivity($dailyCounts, $latestActivityAt, $date, 1);
        });

        self::collectVisitActivity($userId, $start, $end)->each(function (Carbon $date) use (&$dailyCounts, &$latestActivityAt) {
            self::addActivity($dailyCounts, $latestActivityAt, $date, 1);
        });

        $calendar = collect(range(0, $days - 1))->map(function (int $offset) use ($start, $dailyCounts) {
            $date = $start->copy()->addDays($offset);
            $count = $dailyCounts[$date->toDateString()] ?? 0;

            return [
                'date' => $date->toDateString(),
                'label' => $date->format('M d, Y'),
                'count' => $count,
                'level' => self::activityLevel($count),
            ];
        })->values();

        return [
            'streak' => self::currentStreak($dailyCounts, $latestActivityAt),
            'calendar' => $calendar,
            'total' => array_sum($dailyCounts),
        ];
    }

    private static function collectWorkoutActivity(string $userId, Carbon $start, Carbon $end): Collection
    {
        return Workout::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$start, $end])
            ->get()
            ->pluck('completed_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date));
    }

    private static function collectExerciseActivity(string $userId, Carbon $start, Carbon $end): Collection
    {
        return ExerciseLog::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->pluck('created_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date));
    }

    private static function collectProgressActivity(string $userId, Carbon $start, Carbon $end): Collection
    {
        return ProgressMetric::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->pluck('date')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date));
    }

    private static function collectVisitActivity(string $userId, Carbon $start, Carbon $end): Collection
    {
        $user = User::find($userId);

        if (!$user) {
            return collect();
        }

        return collect($user->activity_visit_dates ?? [])
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->startOfDay())
            ->filter(fn (Carbon $date) => $date->betweenIncluded($start, $end));
    }

    private static function addActivity(array &$dailyCounts, ?Carbon &$latestActivityAt, Carbon $date, int $weight): void
    {
        $key = $date->toDateString();
        $dailyCounts[$key] = ($dailyCounts[$key] ?? 0) + $weight;

        if (!$latestActivityAt || $date->greaterThan($latestActivityAt)) {
            $latestActivityAt = $date;
        }
    }

    private static function activityLevel(int $count): int
    {
        return match (true) {
            $count >= 6 => 4,
            $count >= 4 => 3,
            $count >= 2 => 2,
            $count >= 1 => 1,
            default => 0,
        };
    }

    private static function currentStreak(array $dailyCounts, ?Carbon $latestActivityAt): int
    {
        if (!$latestActivityAt || $latestActivityAt->lt(now()->subHours(24))) {
            return 0;
        }

        $streak = 0;
        $cursor = Carbon::parse($latestActivityAt)->startOfDay();

        while (($dailyCounts[$cursor->toDateString()] ?? 0) > 0) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }
}
