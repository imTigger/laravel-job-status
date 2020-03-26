<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Imtigger\LaravelJobStatus\JobStatus;
use Imtigger\LaravelJobStatus\LaravelJobStatusBusServiceProvider;
use Imtigger\LaravelJobStatus\Tests\Data\TestJob;
use Imtigger\LaravelJobStatus\Tests\Data\TestJobWithDatabase;
use Imtigger\LaravelJobStatus\Tests\Data\TestJobWithException;
use Imtigger\LaravelJobStatus\Tests\Data\TestJobWithoutTracking;

class TrackableTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            LaravelJobStatusBusServiceProvider::class,
        ]);
    }

    public function testFinished()
    {
        /** @var TestJobWithDatabase $job */
        $job = new TestJobWithDatabase([
            'status' => 'executing',
        ]);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'status' => 'queued',
        ]);

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

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

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'status' => 'failed',
        ]);
    }

    public function testTrackingDisabled()
    {
        $job = new TestJobWithoutTracking();

        $this->assertNull($job->getJobStatusId());

        $this->assertEquals(0, JobStatus::query()->count());

        app(Dispatcher::class)->dispatch($job);

        $this->assertEquals(0, JobStatus::query()->count());
    }
}
