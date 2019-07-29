<?php

namespace Imtigger\LaravelJobStatus\Tests\Data;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Imtigger\LaravelJobStatus\Trackable;
use Imtigger\LaravelJobStatus\TrackableJob;

class TestJobWithException implements ShouldQueue, TrackableJob
{
    use InteractsWithQueue;
    use Queueable;
    use Dispatchable;
    use Trackable;

    public function __construct()
    {
        $this->prepareStatus();
    }

    public function handle()
    {
        throw new \Exception('test-exception');
    }
}
