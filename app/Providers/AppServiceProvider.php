<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (config('demo.enabled')) {
            config([
                'session.driver' => 'cookie',
                'cache.default' => 'array',
                'queue.default' => 'sync',
                'mail.default' => 'log',
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Vercel terminates HTTPS before forwarding requests to the container.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
