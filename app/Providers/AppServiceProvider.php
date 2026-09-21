<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('jev', function ($app) {
            return new \App\Services\DemoJevManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $simulate = (bool) config('jev.simulate', env('JEV_SIMULATE', true))
            || empty(config('jev.api_key'));

        if ($simulate && ! \Priyanshu\LaravelJev\Facades\Jev::isFaking()) {
            \Priyanshu\LaravelJev\Facades\Jev::fake();
        }
    }
}
