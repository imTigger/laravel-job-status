<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\Constraints\HasInDatabase;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Imtigger\LaravelJobStatus\JobStatusUpdater;
use Imtigger\LaravelJobStatus\Tests\Data\TestJob;
use Imtigger\LaravelJobStatus\Trackable;

class TrackableTest extends TestCase
{
    public function testFinished()
    {
        /** @var TestJob $job */
        $job = new TestJob();

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'status' => 'queued'
        ]);

        app(Dispatcher::class)->dispatch($job);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'status' => 'finished'
        ]);
    }

    public function testStatusFailed()
    {
        $this->expectException(\Exception::class);

        /** @var TestJob $job */
        $job = new class extends TestJob {
            public function handle()
            {
                throw new \Exception('test-exception');
            }
        };

        app(Dispatcher::class)->dispatch($job);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'status' => 'failed'
        ]);
    }

    public function testWithoutPrepareStatus()
    {
        $this->expectException(\Exception::class);

        /** @var TestJob $job */
        $job = new class extends TestJob {
            public function __construct()
            {
            }
        };

        app(Dispatcher::class)->dispatch($job);
    }
}
