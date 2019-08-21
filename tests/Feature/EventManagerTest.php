<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Imtigger\LaravelJobStatus\EventManagers\DefaultEventManager;
use Imtigger\LaravelJobStatus\EventManagers\LegacyEventManager;
use Imtigger\LaravelJobStatus\JobStatus;
use Imtigger\LaravelJobStatus\Tests\Data\TestJob;
use Imtigger\LaravelJobStatus\Tests\Data\TestJobWithException;

class EventManagerTest extends TestCase
{
    /**
     * @dataProvider managerProvider
     */
    public function testManager(string $class, string $status)
    {
        $this->expectException(\Exception::class);

        config()->set('job-status.event_manager', $class);

        /** @var TestJob $job */
        $job = new TestJobWithException();

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'status' => $status,
        ]);
    }

    public function managerProvider()
    {
        return [
            [DefaultEventManager::class, JobStatus::STATUS_FAILED],
            [LegacyEventManager::class, JobStatus::STATUS_RETRYING],
        ];
    }
}
