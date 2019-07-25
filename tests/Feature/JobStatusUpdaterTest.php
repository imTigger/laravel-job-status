<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Illuminate\Contracts\Bus\Dispatcher;
use Imtigger\LaravelJobStatus\JobStatusUpdater;
use Imtigger\LaravelJobStatus\Tests\Data\TestJob;

class JobStatusUpdaterTest extends TestCase
{
    public function testUpdateNonTrackableJob()
    {
        /** @var JobStatusUpdater $updater */
        $updater = app(JobStatusUpdater::class);

        /** @var TestJob $job */
        $job = new TestJob();

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => null,
        ]);

        $updater->update($job, [
            'job_id' => 0,
        ]);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => 0,
        ]);
    }

    public function testUpdateTrackableJob()
    {
        /** @var JobStatusUpdater $updater */
        $updater = app(JobStatusUpdater::class);

        /** @var TestJob $job */
        $job = new TestJob();

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => null,
        ]);

        $updater->update($job, [
            'job_id' => 0,
        ]);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => 0,
        ]);
    }

    public function testUpdateEvent()
    {
        $job = new TestJob();

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => null,
        ]);

        app(Dispatcher::class)->dispatch($job);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => 0,
            'status' => 'finished',
        ]);
    }
}
