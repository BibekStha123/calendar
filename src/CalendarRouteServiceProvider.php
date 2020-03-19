<?php
namespace gas\calendar;

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class CalendarRouteServiceProvider extends RouteServiceProvider
{
    protected $namespace='gas\calendar';

    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        // $this->mapApiRoutes();
        $this->mapWebRoutes();
    }
    
    protected function mapWebRoutes()
    {
        Route::namespace($this->namespace)
            ->group(__DIR__ . '/routes.php');
    }
}


