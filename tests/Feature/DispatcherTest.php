<?php

namespace Imtigger\LaravelJobStatus\Tests\Feature;

use Illuminate\Bus\Dispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\Constraints\HasInDatabase;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Imtigger\LaravelJobStatus\LaravelJobStatusBusServiceProvider;
use Imtigger\LaravelJobStatus\Trackable;

class DispatcherTest extends TestCase
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getPackageProviders($app)
    {
        return parent::getPackageProviders($app) + [
            LaravelJobStatusBusServiceProvider::class
        ];
    }

    public function testDefaultDispatcher()
    {
        $job = new class {
            use InteractsWithQueue, SerializesModels, Queueable, Dispatchable, Trackable, InteractsWithDatabase;

            public function __construct()
            {
                $this->prepareStatus();
            }

            public function handle()
            {
                TestCase::assertThat(
                    'job_statuses', new HasInDatabase($this->getConnection(), [
                        'id' => $this->getJobStatusId(),
                        'job_id' => null,
                    ])
                );
            }

            protected function getConnection()
            {
                $database = app('db');

                return $database->connection($database->getDefaultConnection());
            }
        };

        app(Dispatcher::class)->dispatch($job);
    }

    public function testCustomDispatcher()
    {
        $job = new class {
            use InteractsWithQueue, SerializesModels, Queueable, Dispatchable, Trackable, InteractsWithDatabase;

            public function __construct()
            {
                $this->prepareStatus();
            }

            public function handle()
            {
                TestCase::assertThat(
                    'job_statuses', new HasInDatabase($this->getConnection(), [
                        'id' => $this->getJobStatusId(),
                        'job_id' => null,
                    ])
                );
            }

            protected function getConnection()
            {
                $database = app('db');

                return $database->connection($database->getDefaultConnection());
            }
        };

        app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch($job);
    }
}
