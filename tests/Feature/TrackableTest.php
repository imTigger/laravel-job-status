<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Illuminate\Contracts\Bus\Dispatcher;
use Imtigger\LaravelJobStatus\Tests\Data\TestJob;
use Imtigger\LaravelJobStatus\Tests\Data\TestJobWithException;
use Imtigger\LaravelJobStatus\Tests\Data\TestJobWithoutConstruct;

class TrackableTest extends TestCase
{
    public function testFinished()
    {
        /** @var TestJob $job */
        $job = new TestJob();

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'status' => 'queued',
        ]);

        app(Dispatcher::class)->dispatch($job);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'status' => 'finished',
        ]);
    }

    public function testStatusFailed()
    {
        $this->expectException(\Exception::class);

        /** @var TestJob $job */
        $job = new TestJobWithException();

        app(Dispatcher::class)->dispatch($job);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'status' => 'failed',
        ]);
    }

    public function testWithoutPrepareStatus()
    {
        $this->expectException(\Exception::class);

        $job = new TestJobWithoutConstruct();

        app(Dispatcher::class)->dispatch($job);
    }
}
