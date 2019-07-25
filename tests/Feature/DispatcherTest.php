<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Illuminate\Bus\Dispatcher;
use Imtigger\LaravelJobStatus\LaravelJobStatusBusServiceProvider;
use Imtigger\LaravelJobStatus\Tests\Data\TestJobWithDatabase;

class DispatcherTest extends TestCase
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            LaravelJobStatusBusServiceProvider::class,
        ]);
    }

    public function testDefaultDispatcher()
    {
        $job = new TestJobWithDatabase([
            'job_id' => '',
        ]);

        app(Dispatcher::class)->dispatch($job);
    }

    public function testCustomDispatcher()
    {
        $job = new TestJobWithDatabase([
            'job_id' => 0,
        ]);

        app(\Imtigger\LaravelJobStatus\Dispatcher::class)->dispatch($job);
    }
}
