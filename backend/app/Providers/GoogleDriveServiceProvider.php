<?php

namespace App\Providers;

use App\Services\GoogleDriveService;
use Illuminate\Support\ServiceProvider;

class GoogleDriveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GoogleDriveService::class, function ($app) {
            return new GoogleDriveService();
        });
    }

    public function boot(): void
    {
        //
    }
}