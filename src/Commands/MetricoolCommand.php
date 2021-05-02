<?php

namespace Carloosolrac\Metricool\Commands;

use Illuminate\Console\Command;

class MetricoolCommand extends Command
{
    public $signature = 'laravel-metricool';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
