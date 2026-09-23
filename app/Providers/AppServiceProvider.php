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

        $this->app->alias('jev', \Priyanshu\LaravelJev\Contracts\Jev::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $simulate = (bool) config('jev.simulate', true)
            || empty(config('jev.api_key'));

        if ($simulate && ! Jev::isFaking()) {
            Jev::fake();
        }
    }
}
