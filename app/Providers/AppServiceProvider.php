<?php

namespace App\Providers;

use App\Models\SolicitudFPP01;
use App\Observers\SolicitudFPP01Observer;
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
        SolicitudFPP01::observe(SolicitudFPP01Observer::class);
    }
}
