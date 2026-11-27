<?php

namespace Maarsson\LaravelDbAnonimizer;

use Illuminate\Support\ServiceProvider;

class LaravelDbAnonimizerServiceProvider extends ServiceProvider
{
    /**
     * Load package defaults while preserving the consumer application's configuration.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/anonymization.php', 'anonymization');
    }

    /**
     * Make the default configuration publishable from the consumer application's console.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/anonymization.php' => $this->app->configPath('anonymization.php'),
            ], 'laravel-db-anonimizer-config');
        }
    }
}
