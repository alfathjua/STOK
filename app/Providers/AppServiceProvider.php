<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Trust proxy headers untuk ngrok dan reverse proxy lainnya
        if (app()->environment('local')) {
            URL::forceScheme('https');

            // Set trusted proxies untuk ngrok
            $trustedProxies = request()->server->get('REMOTE_ADDR');
            if ($trustedProxies) {
                request()->setTrustedProxies([$trustedProxies], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
            }
        }
    }
}
