<?php

namespace Imtigger\LaravelJobStatus\Tests\Data;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\Constraints\HasInDatabase;
use Illuminate\Queue\InteractsWithQueue;
use Imtigger\LaravelJobStatus\Tests\Feature\TestCase;
use Imtigger\LaravelJobStatus\Trackable;
use Imtigger\LaravelJobStatus\TrackableJob;

class TestJobWithDatabase implements ShouldQueue, TrackableJob
{
    use InteractsWithQueue;
    use Queueable;
    use Dispatchable;
    use Trackable;
    use InteractsWithDatabase;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;

        $this->prepareStatus();
    }

    public function handle()
    {
        TestCase::assertThat(
            'job_statuses',
            new HasInDatabase($this->getConnection(), [
                'id' => $this->getJobStatusId(),
            ] + $this->data)
        );
    }

    protected function getConnection()
    {
        $database = app('db');

        return $database->connection($database->getDefaultConnection());
    }
}
