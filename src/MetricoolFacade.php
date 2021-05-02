<?php

namespace Carloosolrac\Metricool;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Carloosolrac\Metricool\Metricool
 */
class MetricoolFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-metricool';
    }
}
