<?php

namespace App\Providers;

use App\Support\ActivityStats;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            $user = Auth::user();

            $view->with(
                'layoutStreak',
                $user ? ActivityStats::forUser((string) $user->id, 35)['streak'] : 0
            );
        });
    }
}