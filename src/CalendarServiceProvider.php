<?php

namespace bibek\calendar;

use Illuminate\Support\ServiceProvider;

class CalendarServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //register your controller
        // $this->app->make('bibek\calendar\CalendarController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'calendar');

        $this->app->register('bibek\calendar\CalendarRouteServiceProvider');
        require __DIR__.'/routes.php';

        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/calendar'),
        ], 'calendar');
    }
}
