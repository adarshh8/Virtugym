<?php

namespace App\Http\Middleware;

use App\Support\ActivityStats;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackDailyActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('get') && !$request->expectsJson() && $request->user()) {
            ActivityStats::recordDailyVisit($request->user());
        }

        return $next($request);
    }
}
