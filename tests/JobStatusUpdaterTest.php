<?php

namespace Imtigger\LaravelJobStatus\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Imtigger\LaravelJobStatus\JobStatus;
use Imtigger\LaravelJobStatus\Tests\Data\TrackableJob;
use Imtigger\LaravelJobStatus\Trackable;
use Orchestra\Testbench\TestCase;

class JobStatusUpdaterTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateJob()
    {
        $class = new TrackableJob();
    }
}
