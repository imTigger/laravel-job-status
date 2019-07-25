<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Imtigger\LaravelJobStatus\JobStatusUpdater;
use Imtigger\LaravelJobStatus\Tests\Data\TestJob;

class JobStatusEventsTest extends TestCase
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
}
