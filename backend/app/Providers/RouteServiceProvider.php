<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {

            //Configuracion para el servicio de passport
            Route::prefix('servicio/passport')
                ->middleware('passport')
                ->group(base_path('routes/apis/servicio.php'));

            Route::prefix('servicio/compartido/version_uno')
                ->middleware('compartido')
                ->namespace('App\\Http\\Controllers\\V1\\Compartido')
                ->group(base_path('routes/apis/v1/api_compartido.php'));

            Route::prefix('servicio/hotel/version_uno')
                ->middleware('hotel')
                ->namespace('App\\Http\\Controllers\\V1\\Hotel')
                ->group(base_path('routes/apis/v1/api_hotel.php'));

            /*Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));*/
        });

        $this->mapRestaurantRoutes();
    }

    protected function mapRestaurantRoutes()
    {
        Route::prefix('api/v1/restaurante')
            ->middleware('restaurante')
            ->namespace('App\\Http\\Controllers\\V1\\Restaurante')
            ->group(base_path('routes/apis/v1/api_restaurante.php'));
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('passport', function (Request $request) {
            return Limit::perMinute(5)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for('compartido', function (Request $request) {
            return Limit::perMinute(250)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for('hotel', function (Request $request) {
            return Limit::perMinute(120)->by(optional($request->user())->id ?: $request->ip());
        });

        RateLimiter::for('restaurante', function (Request $request) {
            return Limit::perMinute(800)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
