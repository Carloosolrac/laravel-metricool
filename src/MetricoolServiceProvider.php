<?php

namespace Carloosolrac\Metricool;

use Carloosolrac\Metricool\Commands\MetricoolCommand;
use Illuminate\Support\ServiceProvider;

class MetricoolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel-metricool.php' => config_path('laravel-metricool.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-metricool.php', 'laravel-metricool');
    }
}
