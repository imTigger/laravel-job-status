<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Imtigger\LaravelJobStatus\LaravelJobStatusBusServiceProvider;
use Imtigger\LaravelJobStatus\Tests\Data\TestJob;
use Imtigger\LaravelJobStatus\Tests\Data\TestJobWithDatabase;

class DispatcherTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            LaravelJobStatusBusServiceProvider::class,
        ]);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('queue.default', 'database');
    }

    public function testDefaultDispatcher()
    {
        $job = new TestJob();

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => null,
        ]);

        app(Dispatcher::class)->dispatch($job);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => null,
        ]);
    }

    public function testCustomDispatcher()
    {
        $job = new TestJob();

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => null,
        ]);

        app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch($job);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => 1,
        ]);
    }

    public function testCustomDispatcherChained()
    {
        $job = new TestJobWithDatabase([]);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => null,
        ]);

        TestJob::withChain([
            $job,
        ])->dispatch();

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $this->assertDatabaseHas('job_statuses', [
            'id' => $job->getJobStatusId(),
            'job_id' => 2,
        ]);
    }

    public function testSetup()
    {
        $this->assertInstanceOf(\Imtigger\LaravelJobStatus\Dispatcher::class, app(\Illuminate\Contracts\Bus\Dispatcher::class));
    }
}
