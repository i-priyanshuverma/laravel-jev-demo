<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\DemoJevManager;
use Illuminate\Support\ServiceProvider;
use Priyanshu\LaravelJev\Facades\Jev;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('jev', function ($app) {
            return new DemoJevManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $simulate = (bool) config('jev.simulate', env('JEV_SIMULATE', true))
            || empty(config('jev.api_key'));

        if ($simulate && ! Jev::isFaking()) {
            Jev::fake();
        }
    }
}
